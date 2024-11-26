import os
import json

# 경로 설정
image_folder = "../image/"  # 이미지 폴더 경로
data_file = "../campingSites.json"  # JSON 파일 경로
output_file = "../campingSites_updated.json"  # 수정된 JSON 파일 저장 경로

# JSON 데이터 로드
with open(data_file, "r", encoding="utf-8") as f:
    camp_sites = json.load(f)

# 이미지 경로 매핑
for site in camp_sites:
    image_filename = f"{site['name']}.jpg"  # 캠핑장 이름 기반 파일명
    if os.path.exists(os.path.join(image_folder, image_filename)):
        site["image"] = f"image/{image_filename}"  # 이미지 경로 업데이트
    else:
        print(f"이미지 없음: {site['name']}")  # 누락된 이미지 로그 출력

# JSON 저장
with open(output_file, "w", encoding="utf-8") as f:
    json.dump(camp_sites, f, ensure_ascii=False, indent=4)

print("JSON 파일이 성공적으로 업데이트되었습니다.")
