<script>
    function simpan(event) {
        event.preventDefault();
        let myForm = document.getElementById('formLokasi');
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
            url: "{{ route('warehouse.store') }}",
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
                        location.href = "{{ route('warehouse.index') }}";
                    });
                } else if (data.status == 400) {
                    Swal.fire({
                        text: data.message,
                        icon: "error",
                        buttonsStyling: false,
                        confirmButtonText: "OK",
                        customClass: {
                            confirmButton: "btn btn-danger"
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
                } else if (request.status == 500) {
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

    function update(event) {
        event.preventDefault();
        let myForm = document.getElementById('formLokasi');
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
            url: "{{ route('warehouse.update', ':id') }}".replace(':id', id),
            type: 'POST',
            data: formData,
            contentType: false,
            processData: false,
            success: function(data) {
                if (data.status == 200) {
                    Swal.fire({
                        text: "Data sukses diubah",
                        icon: "success",
                        buttonsStyling: false,
                        confirmButtonText: "Selesai",
                        customClass: {
                            confirmButton: "btn btn-success"
                        }
                    }).then(function(result) {
                        location.href = "{{ route('warehouse.index') }}";
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

    function hapus(id) {
        Swal.fire({
            title: 'Apakah anda yakin ingin menghapus data ini?',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonText: 'Ya, hapus data',
            cancelButtonText: 'Batal',
            customClass: {
                confirmButton: 'btn btn-success',
                cancelButton: 'btn btn-danger'
            }
        }).then(function(result) {
            if (result.isConfirmed) {
                $.ajax({
                    url: "{{ route('warehouse.destroy', ':id') }}".replace(':id', id),
                    type: 'DELETE', 
                    data: {
                        "_token": "{{ csrf_token() }}"
                    },
                    success: function(data) {
                        Swal.fire({
                            text: "Data sukses dihapus",
                            icon: 'success',
                            buttonsStyling: false,
                            confirmButtonText: 'Selesai',
                            customClass: {
                                confirmButton: 'btn btn-success'
                            }
                        }).then(function(result) {
                            location.href = "{{ route('warehouse.index') }}";
                        });
                    },
                    error: function(xhr, status, error) {
                        Swal.fire({
                            text: "Gagal menghapus data",
                            icon: 'error',
                            buttonsStyling: false,
                            confirmButtonText: 'OK',
                            customClass: {
                                confirmButton: 'btn btn-danger'
                            }
                        });
                    }
                });
            }
        });
    }
</script>
