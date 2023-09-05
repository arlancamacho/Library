
<?= $this->Form->create($book, ['id' => 'editForm', 'class' => 'form', 'enctype' => 'multipart/form-data']) ?>
<div class="form-group">
    <?= $this->Form->input('book_name', ['class' => 'form-control']) ?>
</div>
<div class="form-group">
    <?= $this->Form->input('category', ['class' => 'form-control']) ?>
</div>
<div class="form-group">
    <?php if (!empty($book->image)): ?>
        <img src="/library/webroot/img/uploads/<?= h($book->image) ?>" alt="Current Image" width="100">
    <?php endif; ?>
</div>

<div class="form-group">
    <?= $this->Form->input('image', ['type'=> 'file', 'class' => 'form-control']) ?>
</div>

<div class="form-group">
    <?= $this->Form->button('Save Changes', ['class' => 'btn btn-primary']) ?>
</div>
<?= $this->Form->end() ?>


<script>
    $(document).ready(function() {
        // Click event for the "Save Changes" button
        $('#editForm').on('submit', function(e) {
            e.preventDefault(); // Prevent the default form submission

            // Serialize the form data into a format that can be sent via AJAX
            var formData = new FormData(this);    
            // alert(formData);
            var bookId = $(this).data('book-id');

            // Send an AJAX POST request to your update action
            $.ajax({
                url: '/library/books/update/<?= $book->id ?>', // Update with the correct URL
                type: 'POST',
                data: formData,
                processData: false,
                contentType: false,
                success: function(response) {
                    alert('Book Updated successfully');
                    window.location.href = 'http://localhost/library/books';
                },
                error: function(xhr, status, error) {
                    // Handle any errors here
                    console.error(xhr.responseText);
                    // You can display an error message to the user
                }
            });
        });
    });
</script>