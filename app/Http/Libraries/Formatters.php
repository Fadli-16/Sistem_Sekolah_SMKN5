<?php

namespace App\Http\Libraries;

use App\Http\Models\Order;

use App\Http\Models\OrderDetail;

use App\Models\Berkas;
use App\Models\DataPolres;

use Sentinel,Auth;

class Formatters

{

	public static function currencyformat($angka,$satuan='true',$sign='Rp. ',$backsign='')

	{

		$angka = (int) $angka;

		switch ($angka){

			default :

			if ($satuan)

				$output = $sign.number_format($angka,0,",",".").$backsign;

			else

				$output = number_format($angka,0,",",".");

			break;

		}

		return $output;

	}

	public static function tgl_indo($tgl, $format='') {

		$tanggal = substr($tgl,8,2);

		if ($format=='short'){

			$bulan	 = substr($tgl,5,2);

		}elseif ($format=='alphashort'){

			$bulan	 = substr(Formatters::get_bulan(substr($tgl,5,2)),0,3);

		}elseif ($format=='all'){

			$hari = Formatters::get_hari(date('D',strtotime($tgl)));

			$bulan	 = substr($tgl,5,2);

		}else{

			$bulan	 = Formatters::get_bulan(substr($tgl,5,2));

		}



		$tahun	 = substr($tgl,0,4);

		return (isset($hari)) ? $hari.', ' : $tanggal.' '.$bulan.' '.$tahun;

	}



	public static function get_hari($hari){

		$dayList = array(

			'Sun' => 'Minggu',

			'Mon' => 'Senin',

			'Tue' => 'Selasa',

			'Wed' => 'Rabu',

			'Thu' => 'Kamis',

			'Fri' => 'Jumat',

			'Sat' => 'Sabtu'

		);

		return $dayList[$hari];

	}



	public static function get_bulan($bln){

		switch ($bln){

			case 1 :

			return "Januari";

			break;

			case 2:

			return "Februari";

			break;

			case 3:

			return "Maret";

			break;

			case 4:

			return "April";

			break;

			case 5:

			return "Mei";

			break;

			case 6:

			return "Juni";

			break;

			case 7:

			return "Juli";

			break;

			case 8:

			return "Agustus";

			break;

			case 9:

			return "September";

			break;

			case 10:

			return "Oktober";

			break;

			case 11:

			return "November";

			break;

			case 12:

			return "Desember";

			break;

		}

	}

	public static function trim_text($input, $length = 25, $ellipses = true, $ellipses_string = '...', $ellipses_url = '#', $strip_html = true){
    // Ensure input is a string
    if (!is_string($input)) {
        return '';
    }

    // Ensure length is a positive integer
    if (!is_int($length) || $length <= 0) {
        return $input;
    }

    // Strip tags, if desired
    if ($strip_html) {
        $input = strip_tags($input);
    }

    // No need to trim, already shorter than trim length
    if (strlen($input) <= $length) {
        return $input;
    }

    // Find last space within length
    $last_space = strrpos(substr($input, 0, $length), ' ');

    // If no space found, trim at the length
    if ($last_space === false) {
        $trimmed_text = substr($input, 0, $length);
    } else {
        $trimmed_text = substr($input, 0, $last_space);
    }

    // Add ellipses (...)
    if ($ellipses) {
        $trimmed_text .= " " . $ellipses_string;
    }

    return $trimmed_text;
	}

	public static function tokenreplacement($string,$idUser=0,$idInvoice=0)

	{

		if ($idUser>0)

		{

			$user = Sentinel::findById($idUser);

			$target = array(

				'{user_email}',

				'{user_firstname}',

				'{user_lastname}',

				'{user_fullname}',

				'{user_username}',

				'{user_gender}',

				'{user_phone}',

				'{user_address}',

				'{user_province}',

				'{user_city}',

				'{user_subdistrict}',

				'{user_zip}'

			);

			$replacement = array(

				$user->email,

				$user->first_name,

				$user->last_name,

				$user->first_name.' '.$user->last_name,

				$user->username(),

				$user->gender(),

				$user->phone(),

				$user->address(),

				$user->province(),

				$user->city(),

				$user->subdistrict(),

				$user->zip(),

			);

			$string = str_replace($target, $replacement, $string);

		}

		if ($idInvoice>0)

		{

			$order = Order::where('id',$idInvoice)->first();

			$orderDetil = Formatters::invoice_detil($idInvoice);

			$target = array(

				'{invoice_code}',

				'{invoice_detail}',

				'{invoice_uniquenumber}'

			);

			$replacement = array(

				$order->order_code,

				$orderDetil,

				$order->unique_number

			);

			$string = str_replace($target, $replacement, $string);

		}

		return $string;

	}

	public static function invoice_detil($idInvoice)

	{

		$data['invoice'] 		= Order::where('id',$idInvoice)->first();

		$data['invoiceDetail'] 	= OrderDetail::where('order_id',$idInvoice)->get();

		$invoiceDetail = view('emails.invoice_detail')->with('data', $data);

		return $invoiceDetail;

	}

	public static function generate_nomor_surat($berkas_id){
		$berkas = Berkas::find($berkas_id);

		$tempat_melapor = $berkas->tempat_melapor;
		$jenis_laporan = $berkas->jenis_laporan;
		$nama_polres = (!empty($berkas->users)) ? $berkas->users->name : 'BUKAN POLRES';

		if($jenis_laporan=='Orang Hilang'){
			$judul_surat = "SKTLK-OH";
		}else if($jenis_laporan=='Barang Hilang'){
			$judul_surat = "SKTLK-BS";
		}

		$tahun = date('/Y');
		$bulan = date('m/Y');
		$last_surat = Berkas::where('tempat_melapor',$tempat_melapor)->where('no_surat','like','%'.$judul_surat.'%'.$tahun.'%')->where('id_terlapor','!=',$berkas_id)->orderBy('no_surat','DESC')->first();

		$no_surat = '';
		$last_no_surat = '';
		if(!empty($last_surat)){
			$last_no_surat = $last_surat->no_surat;
		}

		if($last_no_surat!=''){
			$arr_last_no_surat = explode("/", $last_no_surat);

			$no_pakai = $arr_last_no_surat[1]+1;

			$no_surat = $judul_surat.'/'.$no_pakai.'/'.$bulan.'/'.$nama_polres;
		}else{
			$no_surat = $judul_surat.'/1/'.$bulan.'/'.$nama_polres;
		}


		return $no_surat;
	}

	public static function rupiah($angka){
		if ($angka != '') {
				$hasil_rupiah = "Rp. " . number_format((int)$angka);
				$hasil_rupiah = str_replace(',','.', $hasil_rupiah);
				return $hasil_rupiah;
		}else {
				return 'Rp. 0';
		}
	}
}
