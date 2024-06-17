import requests
import base64
from io import BytesIO
from PIL import Image
from stegano import lsb

# 이미지 URL
image_url = 'https://onch-1688.s3.ap-northeast-2.amazonaws.com/test/2-tt.jpeg'

# 이미지 다운로드
response = requests.get(image_url)
image_data = BytesIO(response.content)

# 이미지 열기
image = Image.open(image_data)

# 이미지에서 메시지 추출하기
extracted_message_str = lsb.reveal(image)

# 추출된 메시지를 바이너리 형태로 변환
extracted_message_bytes = base64.b64decode(extracted_message_str)

# 바이너리 데이터를 UTF-8로 디코딩하여 원래 메시지 복원
decoded_message = extracted_message_bytes.decode('utf-8')

print(decoded_message)
exit()
