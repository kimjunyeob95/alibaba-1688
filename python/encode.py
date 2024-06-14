import os
import base64
import json
from PIL import Image
from io import BytesIO
from stegano import lsb

file_path = 'public/app/base64.txt'

result = {"success": False, "encoded_base64": None, "error": None}

try:
    # 파일이 존재하는지 확인
    if not os.path.exists(file_path):
        raise Exception("파일이 존재하지 않습니다.")

    # 파일 내용 읽기
    with open(file_path, 'rb') as file:
        file_contents = file.read()

    # base64 디코딩
    image_data = base64.b64decode(file_contents)

    # 이미지 열기
    image = Image.open(BytesIO(image_data))

    # 비밀 메시지 숨기기
    secret_message = "테스트 숨김메세지"

    # 비밀 메시지를 UTF-8로 인코딩한 후 바이너리 형태로 변환
    secret_message_bytes = secret_message.encode('utf-8')

    # 비밀 메시지를 문자열로 변환하여 숨기기
    secret_message_str = base64.b64encode(secret_message_bytes).decode('ascii')

    # 이미지를 LSB 기법으로 숨김
    encoded_image = lsb.hide(image, secret_message_str)

    # 이미지 저장 없이 base64로 변환하기 (항상 PNG 형식 사용)
    buffered = BytesIO()
    encoded_image.save(buffered, format="PNG")
    encoded_base64 = base64.b64encode(buffered.getvalue()).decode('utf-8')

    result["success"] = True
    result["encoded_base64"] = encoded_base64

except Exception as e:
    result["error"] = str(e)

# JSON 형식으로 출력
print(json.dumps(result))
exit()