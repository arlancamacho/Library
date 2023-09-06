<div class="container">
    <div class="row">
        <div class="form group">
        <p class="h1">Add Books</p>
        <p>This is sample change and checkout</p>
        <hr>
        </div>
    </div>
</div>
<div class="container">
    <div class="row">
        <form id="csvUploadForm" class="form-horizontal" action="/library/books/upload" method="POST" enctype="multipart/form-data">
            <div class="form-group">
                    <label for="csv_file">CSV Upload File</label>
                    <input type="file" class="form-control" name="csv_file" id="csv_file" accept=".csv">
            </div>
            <div class="form-group">
                <button type="submit" class="btn btn-primary">Upload CSV</button>
            </div>
        </form>
    </div>
</div>

<div class="container">
    <div class="row">
        <?= $this->Flash->render() ?>
        <?= $this->Form->create($book, ['id' => 'addBookForm', 'type' => 'form','enctype' => 'multipart/form-data']) ?>
        <div class="form-group">
            <?= $this->Form->input('book_name', [ 'id' => 'bookName', 'class' => 'form-control']) ?>
        </div>
        <div class="form-group">
            <?= $this->Form->input('category', ['class' => 'form-control']) ?>
        </div>
        <div class="form-group">
            <?= $this->Form->input('image',['type' => 'file', 'name' => 'image', 'id'=> 'imageInput', 'class' => 'form-control']) ?>
        </div>
        <div class="form-group">
            <?= $this->Form->button('Add Book', ['class' => 'btn btn-primary']) ?>
        </div>
        <?= $this->Form->end() ?>
    </div>
</div>


<script>
  
    $(document).ready(function() {
        var csrfToken = <?= json_encode($this->request->getParam('_csrfToken')) ?>;
    $('#addBookForm').submit(function(event) {
        event.preventDefault();

        // var formData = $('#addBookForm').serialize();
        var formData = new FormData();
        var imageInput = document.getElementById('imageInput');

        if (imageInput.files.length > 0) {
            formData.append('image', imageInput.files[0]);
        }
        var bookName = $('#bookName').val();
        var category = $('#category').val();

        formData.append('book_name', bookName);
        formData.append('category', category);

        $.ajax({
            url: '/library/books/add', 
            type: 'POST',
            data: formData,
            processData: false,
            contentType: false,
            headers: {
                    'X-CSRF-Token': csrfToken // Include the CSRF token in headers
                    },
            success: function(response) {
                alert('Book Added successfully');
                window.location.href = 'http://localhost/library/books';
            },
            error: function(xhr, status, error) {
                // Handle any errors here
                console.error(xhr.responseText);
            }
        });
    });

    $('#csvUploadForm').submit(function(e) {
        e.preventDefault(); // Prevent the default form submission
        var formData = new FormData(this);
        $.ajax({
            type: 'POST',
            url: '/library/books/upload',
            data: formData,
            headers: {
                    'X-CSRF-Token': csrfToken // Include the CSRF token in headers
                    },
            processData: false,
            contentType: false,
            success: function(response) {
                alert('Book Added successfully');
                window.location.href = 'http://localhost/library/books';
            },
            error: function(xhr, status, error) {
                // Handle errors (e.g., display an error message)
                console.error(error);
            }
        });
    });
});

</script>