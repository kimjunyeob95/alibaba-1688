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

function fn_split(keyword) {
    // 줄바꿈을 콤마로 변경하고 앞뒤 공백을 제거
    let text = keyword.replace(/(\r\n|\r|\n)/g, ",").trim();
    // 콤마를 기준으로 배열 생성
    text = text.split(',');
    // 각 배열 요소의 앞뒤 공백 제거
    text = text.map(url => url.trim());
    // 빈 값을 제거
    text = text.filter(url => url);
    // 중복 제거
    text = [...new Set(text)];
    
    return text;
}