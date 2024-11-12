<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Upload ZIP</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <style>
        body {
            font-family: Arial, sans-serif;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            margin: 0;
            background-color: #f4f4f4;
        }
        .container {
            background: #fff;
            padding: 20px;
            border-radius: 5px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
            width: 100%;
            max-width: 500px;
        }
        .message {
            margin-top: 20px;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>Upload File</h1>
        <form id="draft-upload-form" action="{{ route('check') }}" enctype="multipart/form-data" method="POST">
            @csrf
            <div class="form-group">
                <label for="">Upload Rar</label>
                <input type="file" name="rar" id="rar" class="form-control" required>
            </div>

            <div class="form-group">
                <label for="">Upload Excel</label>
                <input class="form-control" type="file" name="excel" id="excel" accept=".xls,.ods,.xlsx">
            </div>

            <button type="submit" class="btn btn-primary">Upload</button>
        </form>
        <div class="message mt-3" id="message"></div>
    </div>

    <!-- jQuery -->
    <script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
    <script>
        $('#draft-upload-form').on('submit', function (e) {
            e.preventDefault()
            
                var data = new FormData(this)

                $.ajax({
                    url: $(this).attr('action'),
                    type: 'POST',
                    data: data,
                    cache: false,
                    contentType: false,
                    processData: false,
                    success: function (response) {
                        console.log('berhasil')
                    },
                    error: function (error) {
                        toastr.error('Data gagal di upload')
                    }
                })
        })

        // $(document).ready(function() {
        //     $('#uploadForm').on('submit', function(e) {
        //         e.preventDefault(); // Mencegah reload halaman

        //         var formData = new FormData(this);

        //         $.ajax({
        //             url: '/api/upload-zip', // Endpoint API Laravel
        //             type: 'POST',
        //             data: formData,
        //             processData: false,
        //             contentType: false,
        //             success: function(response) {
        //                 $('#message').html('<div class="alert alert-success">' + response.message + '</div>');
        //             },
        //             error: function(xhr) {
        //                 var errorMessage = 'An error occurred. Please try again.';
        //                 if (xhr.responseJSON && xhr.responseJSON.message) {
        //                     errorMessage = xhr.responseJSON.message;
        //                 }
        //                 $('#message').html('<div class="alert alert-danger">' + errorMessage + '</div>');
        //             }
        //         });
        //     });
        // });
    </script>
</body>
</html>
