// Add this function if it's not already there
function logout() {
    Swal.fire({
        title: "Apakah anda yakin?",
        text: "Anda akan keluar dari akun",
        icon: "question",
        showCancelButton: true,
        confirmButtonColor: '#4ecdc4',
        cancelButtonColor: '#dc3545',
        confirmButtonText: 'Ya, Logout!',
        cancelButtonText: 'Batal'
    }).then((result) => {
        if (result.isConfirmed) {
            document.getElementById('logoutForm').submit();
        }
    });
}