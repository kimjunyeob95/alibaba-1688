$(document).ready(function(){
    $(document).on('click', '.preview-image', function() {
        var imageSource = $(this).attr('src');
        $('#previewImage').attr('src', imageSource);
        $('#imagePreviewModal').modal('show');
    });

    $(".lazy-img").lazy();
});