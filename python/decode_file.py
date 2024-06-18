import os
import base64
from io import BytesIO
from PIL import Image
from stegano import lsb

# 파일 경로
file_path = 'public/app/2-tt.png'

result = {"success": False, "decoded_message": None, "error": None}

try:
    # 파일이 존재하는지 확인
    if not os.path.exists(file_path):
        raise Exception("파일이 존재하지 않습니다.")

    # 파일 읽기
    with open(file_path, 'rb') as file:
        file_contents = file.read()

    # 이미지 열기
    image = Image.open(BytesIO(file_contents))

    # 이미지에서 메시지 추출하기
    extracted_message_str = lsb.reveal(image)

    # 추출된 메시지를 바이너리 형태로 변환
    extracted_message_bytes = base64.b64decode(extracted_message_str)

    # 바이너리 데이터를 UTF-8로 디코딩하여 원래 메시지 복원
    decoded_message = extracted_message_bytes.decode('utf-8')

    # 결과 업데이트
    result["success"] = True
    result["decoded_message"] = decoded_message

except Exception as e:
    result["error"] = str(e)

print(result)
