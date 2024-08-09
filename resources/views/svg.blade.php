@extends('dashboard.base')

@section('styles')
    <style>
        #iconGrid {
            display: grid;
            grid-template-columns: repeat(7, 1fr);
            gap: 20px;
            max-width: 1500px;
            margin: 0 auto;
        }
        .icon-item {
            display: flex;
            flex-direction: column;
            align-items: center;
            text-align: center;
            padding: 20px;
            border: 1px solid #e0e0e0;
            border-radius: 8px;
        }
        .icon {
            width: 400px;
            height: 400px;
            margin-bottom: 10px;
        }
        .icon-name {
            font-size: 14px;
            word-break: break-all;
        }
    </style>
@endsection

@section('scripts')
@endsection

@section('content')
    <div class="container-fluid">
        <div class="row my-4 bg-white py-3">
            <div class="col-12 mb-3">

                <div class="icon-grid" id="iconGrid"></div>
            </div>
        </div>
    </div>
<script type="text/javascript">

    const svgUrl = '/vendors/@coreui/icons/svg/free.svg';

    fetch(svgUrl)
        .then(response => response.text())
        .then(svgContent => {
            const parser = new DOMParser();
            const svgDoc = parser.parseFromString(svgContent, 'image/svg+xml');
            const symbols = svgDoc.querySelectorAll('symbol');
            const iconGrid = document.getElementById('iconGrid');

            symbols.forEach(symbol => {
                const iconId = symbol.id;
                const iconItem = document.createElement('div');
                iconItem.className = 'icon-item';
                
                // SVG 요소 생성 및 속성 설정
                const svgElement = document.createElementNS("http://www.w3.org/2000/svg", "svg");
                svgElement.setAttribute("width", "60");
                svgElement.setAttribute("height", "60");
                svgElement.setAttribute("viewBox", "0 0 512 512"); // 원본 viewBox 유지
                
                const useElement = document.createElementNS("http://www.w3.org/2000/svg", "use");
                useElement.setAttributeNS("http://www.w3.org/1999/xlink", "xlink:href", `${svgUrl}#${iconId}`);
                
                svgElement.appendChild(useElement);
                
                iconItem.appendChild(svgElement);
                iconItem.innerHTML += `<span class="icon-name">${iconId}</span>`;
                
                iconGrid.appendChild(iconItem);
            });
        })
        .catch(error => console.error('Error loading SVG:', error));
</script>

@endsection
