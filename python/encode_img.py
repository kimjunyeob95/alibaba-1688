import sys
import os
import base64
import json
from PIL import Image
from io import BytesIO
from stegano import lsb

result = {"success": False, "encoded_base64": None, "error": None}

try:
    json_file_path   = sys.argv[1]
    base64_file_path = sys.argv[2]
    
    if not json_file_path:
        raise Exception("Empty JSON file path")
    if not os.path.exists(json_file_path):
        raise Exception("JSON file does not exist")
    with open(json_file_path, 'r', encoding='utf-8') as f:
        json_data = f.read()
        if not json_data:
            raise Exception("JSON file is empty")
        json_data = json.loads(json_data)
    
    if not base64_file_path:
        raise Exception("Empty base64 file path")
    if not os.path.exists(base64_file_path):
        raise Exception("Base64 file does not exist")
    with open(base64_file_path, 'r', encoding='utf-8') as f:
        base64_data = f.read()
        if not base64_data:
            raise Exception("Base64 file is empty")
    
    # base64 디코딩
    image_data = base64.b64decode(base64_data)

    # 이미지 열기
    image = Image.open(BytesIO(image_data))
    
    # 이미지 확장자 가져오기
    image_format = image.format

    # 비밀 메시지 숨기기
    secret_message = json.dumps(json_data, ensure_ascii=False)

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