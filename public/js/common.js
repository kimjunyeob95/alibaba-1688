$(document).ready(function(){
    $(document).on('click', '.preview-image', function() {
        var imageSource = $(this).attr('src');
        $('#previewImage').attr('src', imageSource);
        $('#imagePreviewModal').modal('show');
    });

    $('#allCheckbox').click(function(){
        let checked = $(this).is(":checked");
        $(".chk-inp").prop("checked", checked);
    });

    $(".lazy-img").lazy();
});