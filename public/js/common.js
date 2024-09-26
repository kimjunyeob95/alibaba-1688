$(document).ready(function(){
    $(document).on('click', '.preview-image', function() {
        var imageSource = $(this).attr('src');
        $('#previewImage').attr('src', imageSource);
        $('#imagePreviewModal').modal('show');
    });

    $('#allCheckbox').click(function(){
        let checked = $(this).is(":checked");
        $(".chk-inp:not(:disabled)").prop("checked", checked);
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

function toggleCheckbox(event, cardElement) {
    // 클릭된 요소가 a 태그나 그 자식 요소인 경우 아무 것도 하지 않음
    if (event.target.closest('a')) {
        return;
    }

    // 카드 내 체크박스 요소 찾기
    const checkbox = cardElement.querySelector('.form-check-input');
    if (checkbox) {
        checkbox.checked = !checkbox.checked; // 체크박스 상태 토글
    }
}

function resizeImage(file, maxSize) {
    return new Promise((resolve, reject) => {
        const reader = new FileReader();
        reader.readAsDataURL(file);
        reader.onload = function(event) {
            const img = new Image();
            img.src = event.target.result;
            img.onload = function() {
                let width = img.width;
                let height = img.height;

                const canvas = document.createElement('canvas');
                const ctx = canvas.getContext('2d');
                canvas.width = width;
                canvas.height = height;
                ctx.drawImage(img, 0, 0, width, height);

                let quality = 0.9;
                let dataUrl = canvas.toDataURL('image/jpeg', quality);

                while (dataUrl.length > maxSize && quality > 0.1) {
                    quality -= 0.05;
                    dataUrl = canvas.toDataURL('image/jpeg', quality);
                }

                if (dataUrl.length < maxSize) {
                    resolve(dataURLtoFile(dataUrl, file.name));
                } else {
                    reject('파일 크기를 충분히 줄일 수 없습니다.');
                }
            };
        };
    });
}

function dataURLtoFile(dataUrl, fileName) {
    const arr = dataUrl.split(',');
    const mime = arr[0].match(/:(.*?);/)[1];
    const bstr = atob(arr[1]);
    let n = bstr.length;
    const u8arr = new Uint8Array(n);

    while(n--){
        u8arr[n] = bstr.charCodeAt(n);
    }

    return new File([u8arr], fileName, {type:mime});
}

/**
 * 날짜 문자열을 지정된 형식으로 변환합니다.
 * @param {string} dateString - 'YYYYMMDD' 형식의 날짜 문자열
 * @param {string} [format='yyyy-mm-dd'] - 원하는 출력 형식
 * @returns {string} 변환된 날짜 문자열
 */
function formatDate(dateString, format = "yyyy-mm-dd") {
    if (!dateString || dateString.length !== 8) return '';

    const year   = dateString.substring(0, 4);
    const month  = dateString.substring(4, 6);
    const day    = dateString.substring(6, 8);

    let result = format;
        result = result.replace('yyyy', year);
        result = result.replace('mm', month);
        result = result.replace('dd', day);

    return result;
}