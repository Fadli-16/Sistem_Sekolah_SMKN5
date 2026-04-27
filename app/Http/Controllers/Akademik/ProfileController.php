<?php

namespace App\Http\Controllers\Akademik;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;
use App\Models\User;
use App\Models\AdminProfile;

/**
 * ProfileController handles show/update of profile for all roles.
 */
class ProfileController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Show profile page for authenticated user.
     */
    public function show()
    {
        /** @var User $user */
        $user = Auth::user();
        $user->load(['siswa', 'guru', 'adminProfile']);

        return view('sistem_akademik.profile', [
            'user' => $user,
            'title' => 'Profile'
        ]);
    }

    /**
     * Update basic profile fields.
     */
    public function updateProfile(Request $request)
    {
        /** @var User $user */
        $user = Auth::user();

        $rules = [
            'nama' => 'required|string|max:255',
            'email' => 'nullable|email|unique:users,email,' . $user->id,
            'tanggal_lahir' => 'nullable|date',
            'jenis_kelamin' => 'nullable|in:Laki-laki,Perempuan',
            'alamat' => 'nullable|string|max:2000',
            'no_hp' => 'nullable|string|max:50',
            'agama' => 'nullable|string|max:100',
            'jurusan' => 'nullable|string|max:255',
        ];

        $data = $request->validate($rules);
        $isRestricted = (bool) ($user->siswa || $user->guru);

        if ($isRestricted) {
            unset($data['nama'], $data['jurusan']);
        }

        $updateUser = [];
        if (isset($data['nama'])) $updateUser['nama'] = $data['nama'];
        if (isset($data['email'])) $updateUser['email'] = $data['email'];

        if (!empty($updateUser)) {
            $user->update($updateUser);
        } else {
        }

        $allowedProfileFields = ['tanggal_lahir', 'jenis_kelamin', 'alamat', 'no_hp', 'agama', 'jurusan'];
        if ($isRestricted) {
            $allowedProfileFields = array_diff($allowedProfileFields, ['jurusan']);
        }

        $profilePayload = [];
        foreach ($allowedProfileFields as $f) {
            if (array_key_exists($f, $data)) {
                $profilePayload[$f] = $data[$f];
            }
        }

        // filter out nulls so we don't overwrite with null
        $filtered = array_filter($profilePayload, function ($v) {
            return $v !== null && $v !== '';
        });

        if ($user->siswa) {
            $user->siswa->update($filtered);
        } elseif ($user->guru) {
            $user->guru->update($filtered);
        } else {
            AdminProfile::updateOrCreate(['user_id' => $user->id], $filtered);
        }

        return back()->with('status', 'profile-updated');
    }

    /**
     * Update profile photo.
     */
    public function updatePhoto(Request $request)
    {
        $request->validate([
            'image' => 'required|image|mimes:jpeg,png,jpg,gif,svg|max:4096',
        ]);

        $user = $request->user();

        $model = null;
        if ($user->relationLoaded('siswa') || $user->siswa) {
            $model = $user->siswa;
            $field = 'image';
        } elseif ($user->relationLoaded('guru') || $user->guru) {
            $model = $user->guru;
            $field = 'image';
        } elseif ($user->relationLoaded('adminProfile') || $user->adminProfile) {
            $model = $user->adminProfile;
            $field = 'image';
        } else {
            return $request->wantsJson()
                ? response()->json(['success' => false, 'message' => 'Tidak ada profil terkait untuk menyimpan foto.'], 422)
                : redirect()->back()->with('status', 'error')->with('message', 'Tidak ada profil terkait.');
        }

        $file = $request->file('image');
        $name = time() . '_' . Str::slug(pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME)) . '.' . $file->getClientOriginalExtension();
        $dest = public_path('assets/profile');

        if (!File::exists($dest)) {
            File::makeDirectory($dest, 0755, true);
        }

        $file->move($dest, $name);

        if (!empty($model->{$field})) {
            $old = $dest . DIRECTORY_SEPARATOR . $model->{$field};
            if (File::exists($old)) {
                @unlink($old);
            }
        }

        $model->{$field} = $name;
        $model->save();
        $url = asset('assets/profile/' . $name);

        if ($request->ajax() || $request->wantsJson()) {
            return response()->json(['success' => true, 'file' => $name, 'url' => $url]);
        }

        return redirect()->back()->with('status', 'photo-updated')->with('message', 'Foto profil berhasil diperbarui.');
    }

    /**
     * Update password.
     */
    public function updatePassword(Request $request)
    {
        /** @var User $user */
        $user = Auth::user();

        $request->validate([
            'current_password' => 'required|string',
            'password' => 'required|string|confirmed|min:5',
        ]);

        if (!Hash::check($request->current_password, $user->password)) {
            return back()->withErrors(['current_password' => 'Password saat ini tidak cocok.']);
        }

        $user->update(['password' => Hash::make($request->password)]);

        return back()->with('status', 'password-updated');
    }
}
