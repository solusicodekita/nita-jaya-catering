<script>
    function togglePassword(field) {
        const input = document.getElementById(field);
        const eye = document.getElementById('eye-' + field);

        if (input.type === 'password') {
            input.type = 'text';
            eye.classList.remove('fa-eye');
            eye.classList.add('fa-eye-slash');
        } else {
            input.type = 'password';
            eye.classList.remove('fa-eye-slash');
            eye.classList.add('fa-eye');
        }
    }

    document.getElementById('new_password_confirmation').addEventListener('input', function() {
        const password = document.getElementById('new_password').value;
        const confirmation = this.value;
        const matchMessage = document.getElementById('password-match');
        const submitBtn = document.getElementById('submit-btn');

        if (password !== confirmation) {
            matchMessage.style.display = 'block';
            submitBtn.disabled = true;
        } else {
            matchMessage.style.display = 'none';
            submitBtn.disabled = false;
        }
    });

    function simpan(event) {
        event.preventDefault();
        let myForm = document.getElementById('formPassword');
        let formData = new FormData(myForm);
        Swal.fire({
            title: 'Sedang diproses',
            html: 'Mohon ditunggu sampai selesai',
            allowOutsideClick: false,
            allowEscapeKey: false,
            timerProgressBar: true,
            didOpen: () => {
                Swal.showLoading()
            },
        })
        $.ajax({
            url: "{{ route('pengaturan.updatePassword') }}",
            type: 'POST',
            data: formData,
            contentType: false,
            processData: false,
            success: function(data) {
                if (data.status == 200) {
                    Swal.fire({
                        text: "Data sukses tersimpan pada ",
                        icon: "success",
                        buttonsStyling: false,
                        confirmButtonText: "Selesai",
                        customClass: {
                            confirmButton: "btn btn-success"
                        }
                    }).then(function(result) {
                        fetch("{{ route('logout') }}", {
                            method: "POST",
                            headers: {
                                "X-CSRF-TOKEN": "{{ csrf_token() }}",
                                "Content-Type": "application/json"
                            }
                        }).then(() => {
                            location.href =
                            "{{ route('login') }}"; // redirect ke halaman login atau lainnya
                        });
                    });
                } else if (data.status == 400) {
                    Swal.fire({
                        text: data.msg,
                        icon: "error",
                        buttonsStyling: false,
                        confirmButtonText: "Selesai",
                        customClass: {
                            confirmButton: "btn btn-success"
                        }
                    })
                }
            },
            error: function(request, status, error) {
                if (request.status === 422) {
                    let errors = request.responseJSON.errors;
                    let messages = '';
                    Object.keys(errors).forEach(function(key) {
                        messages += '&bull; ' + errors[key][0] + '<br>';
                    });

                    Swal.fire({
                        title: "Error",
                        html: messages,
                        icon: "error",
                        confirmButtonText: "OK",
                        customClass: {
                            confirmButton: "btn btn-danger"
                        }
                    });
                }else if (request.status == 500) {
                    Swal.fire({
                        text: request.responseJSON.message,
                        icon: "error",
                        buttonsStyling: false,
                        confirmButtonText: "OK",
                        customClass: {
                            confirmButton: "btn btn-danger"
                        }
                    })
                } else {
                    errorMessage(request);
                }
            }

        });
    }
</script>
