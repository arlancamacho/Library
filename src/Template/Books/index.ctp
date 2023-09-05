<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
        <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.1/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-4bw+/aepP/YC94hEpVNVgiZdgIC5+VKNBQNGCHeKRQN+PtmoHDEXuppvnDJzQIu9" crossorigin="anonymous">
        <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.1/dist/js/bootstrap.bundle.min.js" integrity="sha384-HwwvtgBNo3bZJJLYd8oVXjrBZt8cqVSpeBNS5n7C8IVInixGAoxmnlMuBnhbgrkm" crossorigin="anonymous"></script>
        <!-- Add DataTables CSS -->
        <!-- <link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/1.10.25/css/jquery.dataTables.css"> -->

        <!-- Add DataTables JS and jQuery -->

        <script src="https://code.jquery.com/jquery-3.7.1.slim.min.js" integrity="sha256-kmHvs0B+OpCW5GVHUNjv9rOmY0IvSIRcf7zGUDTDQM8="crossorigin="anonymous"></script>
        <!-- Include Bootstrap CSS -->
        <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
        <!-- Include jQuery and Bootstrap JS -->
        <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
        <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>

        <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
        <link rel="stylesheet" href="https://cdn.datatables.net/1.10.25/css/jquery.dataTables.min.css">
        <script src="https://cdn.datatables.net/1.10.25/js/jquery.dataTables.min.js"></script>
        <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
        <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.16.0/umd/popper.min.js"></script>    
<body>
</body>
</html>
<div class="container">
    <div class="container mt-3">
        <h2>List of Books</h2>
        <button id="openModalButton" class="btn btn-primary" data-toggle="modal" data-target="#bookModal">Add Book</button>
        <a href="/library/books/export">Export Data</a>
        <div class="col-md-2 leftsidemenu">
        <div class="col-md-2 leftsidemenu">
    </div>
</div>
<div id="delete-message"></div>
<div id="editModal" class="modal" style="display: none;">
        <div class="modal-content">
            <div id="editFormContainer"></div>
        </div>
    </div>
<table id="dataTable" class="table bordered display" style="width:100%">
    <thead>
        <tr>
            <th>Id</th>
            <th>Book name</th>
            <th>Category</th>
            <th>Image</th>
            <th>Actions</th>
        </tr>
    </thead>
    <tbody>
        
    </tbody>
</table>
                                        <!-- THIS IS EDIT MODAL -->
<div class="modal fade" id="exampleModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="exampleModalLabel">Edit Books</h5>
        <button type="button" id="closeBtn" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body">
        
      </div>
      <div class="modal-footer">
      </div>
    </div>
  </div>
</div>
                                        <!-- THIS IS ADD MODAL -->
<div class="modal fade" id="bookModal" tabindex="-1" role="dialog" aria-labelledby="bookModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <!-- Content will be loaded via AJAX -->
        </div>
    </div>
</div>
                                        <!-- THIS IS IMAGE PREVIEW MODAL -->
<div class="modal fade" id="imageModal" tabindex="-1" role="dialog" aria-labelledby="imageModalLabel" aria-hidden="true">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="imageModalLabel">Image Preview</h5>
        <button type="button" id="closeImageView" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body">
        <img id="previewImage" src="" alt="Image Preview" class="img-fluid">
      </div>
    </div>
  </div>
</div>

<script>
    
$(document).ready(function() {
    var csrfToken = <?= json_encode($this->request->getParam('_csrfToken')) ?>;
    var dataTable = $('#dataTable').DataTable({
        processing: true,
        serverSide: true,
        // paging: true,
        ajax: {
            url: '<?= $this->Url->build(['controller' => 'Books', 'action' => 'data']) ?>',
            type: 'POST',
            headers: {
            'X-CSRF-Token': csrfToken
            },
        },
        columns: [
            { data: 'id' },
            { data: 'book_name' },
            { data: 'category' },
            {
            data: 'image',
            render: function (data, type, row) {
                if (type === 'display' && data) {
                    var imageUrl = '/library/webroot/img/uploads/' + data; // Update the path as needed
                    return '<img class="imageView" data-toggle="modal" data-image-source="' + imageUrl + '" data-target="#imageModal" src="' + imageUrl + '" alt="Book Image" width="100">';
                }
                return data;
            }
        },
            { data: 'actions' },
      ]
    });

    $('#dataTable').on('click', '.imageView', function () {
        var imageSource = $(this).data('image-source');
        $('#previewImage').attr('src', imageSource);
        $('#imageModal').modal('show'); // Show the modal
    });

    var csrfToken = <?= json_encode($this->request->getParam('_csrfToken')) ?>;
    $('#dataTable').on('click', '.delete-btn', function(e) {
        e.preventDefault();
        var bookId = $(this).data('book-id');
        if (confirm('Are you sure you want to delete this book?')) {
                $.ajax({
                    url: '/library/books/delete/' + bookId,
                    type: 'DELETE',
                    dataType: 'json',
                    headers: {
                    'X-CSRF-Token': csrfToken // Include the CSRF token in headers
                    },
                    success: function (response) {
                        if (response.message === 'Book deleted successfully') {
                            // Remove the row from the table
                            $('#row_' + bookId).remove();
                            alert('Book deleted successfully');
                            window.location.href = 'http://localhost/library/books';
                        } else {
                            alert('Error deleting the book');
                        }
                    },
                    error: function () {
                        alert('Error deleting the book');
                    }
                });
            }
    });
    $('#dataTable').on('click', '.edit-btn', function(e) {
            e.preventDefault();
            var bookId = $(this).data('book-id');// Get the book ID from the data attribute
        
            // Send an AJAX request to the edit route
            $.ajax({
                url: '/library/books/edit/' + bookId, // Use the correct URL for your route
                type: 'GET',
                success: function (data) {
                    // Display the response data (e.g., the edit form) in a modal
                    $('#exampleModal .modal-body').html(data);
                    $('#exampleModal').modal('show'); // Show the modal
                },
                error: function (xhr, status, error) {
                    console.error(error);
                    alert('An error occurred while loading the edit form.');
                }
            });
    });

    $('#openModalButton').click(function() {
        $.ajax({
            url: '/library/books/add',
            type: 'GET',
            success: function(response) {
                $('#bookModal .modal-content').html(response);
            },
            error: function() {
                alert('Error loading the form.');
            }
        });
    });
});


$("#closeBtn").on("click", function() {
  $("#exampleModal").modal("hide"); // close modal
});

$("#closeImageView").on("click", function() {
  $("#imageModal").modal("hide"); // close modal
});

</script>




