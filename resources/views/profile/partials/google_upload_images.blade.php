<section>
    <header>
        <h2 class="text-lg font-medium text-gray-900">
            {{ __('google 多張圖片上傳') }}
        </h2>
    </header>



    <div class="container">

        <h2>拖曳多張照片上傳</h2>

        @if(session('success'))
        <div class="success">
            {{ session('success') }}
        </div>
        @endif

        @if($errors->any())
        <div class="error">
            <ul>
                @foreach($errors->all() as $error)
                <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
        @endif

        <div class="drop-zone" id="dropZone">
            <p>
                拖曳圖片到此處<br>
                或點擊選擇圖片
            </p>

            <input type="file" id="photos" name="photos[]" multiple accept="image/*">
        </div>

        <div class="preview-container" id="preview"></div>

        <button onclick="uploadFiles()" type="submit" class="submit-btn">
            上傳圖片
        </button>

        <br>

        <div id="progressContainer"></div>


    </div>

</section>

<script src="https://cdn.jsdelivr.net/npm/axios/dist/axios.min.js"></script>
<script>
    const dropZone = document.getElementById('dropZone');
    const fileInput = document.getElementById('photos');
    const preview = document.getElementById('preview');

    let selectedFiles = [];

    /*
    |--------------------------------------------------------------------------
    | 點擊開啟檔案選擇
    |--------------------------------------------------------------------------
    */
    dropZone.addEventListener('click', () => {
        fileInput.click();
    });

    /*
    |--------------------------------------------------------------------------
    | 拖曳效果
    |--------------------------------------------------------------------------
    */
    dropZone.addEventListener('dragover', (e) => {
        e.preventDefault();
        dropZone.classList.add('dragover');
    });

    dropZone.addEventListener('dragleave', () => {
        dropZone.classList.remove('dragover');
    });

    /*
    |--------------------------------------------------------------------------
    | 放下檔案
    |--------------------------------------------------------------------------
    */
    dropZone.addEventListener('drop', (e) => {

        e.preventDefault();

        dropZone.classList.remove('dragover');

        const files = Array.from(e.dataTransfer.files);

        handleFiles(files);
    });

    /*
    |--------------------------------------------------------------------------
    | 手動選擇檔案
    |--------------------------------------------------------------------------
    */
    fileInput.addEventListener('change', (e) => {

        const files = Array.from(e.target.files);

        handleFiles(files);
    });

    /*
    |--------------------------------------------------------------------------
    | 處理檔案
    |--------------------------------------------------------------------------
    */
    function handleFiles(files) {

        files.forEach(file => {

            if (!file.type.startsWith('image/')) {
                return;
            }

            selectedFiles.push(file);
        });

        renderPreview();
        updateInputFiles();
    }

    /*
    |--------------------------------------------------------------------------
    | 顯示圖片預覽
    |--------------------------------------------------------------------------
    */
    function renderPreview() {

        preview.innerHTML = '';

        selectedFiles.forEach((file, index) => {

            const reader = new FileReader();

            reader.onload = function(e) {

                const wrapper = document.createElement('div');
                wrapper.classList.add('preview-item');

                const img = document.createElement('img');
                img.src = e.target.result;
                img.classList.add('preview-image');

                const removeBtn = document.createElement('button');
                removeBtn.innerHTML = '×';
                removeBtn.classList.add('remove-btn');

                removeBtn.addEventListener('click', () => {

                    selectedFiles.splice(index, 1);

                    renderPreview();
                    updateInputFiles();
                });

                wrapper.appendChild(img);
                wrapper.appendChild(removeBtn);

                preview.appendChild(wrapper);
            };

            reader.readAsDataURL(file);
        });

        console.log( selectedFiles )

    }

    /*
    |--------------------------------------------------------------------------
    | 更新 input file
    |--------------------------------------------------------------------------
    */
    function updateInputFiles() {

        const dataTransfer = new DataTransfer();

        selectedFiles.forEach(file => {
            dataTransfer.items.add(file);
        });

        fileInput.files = dataTransfer.files;

    
    }

    /*
    |--------------------------------------------------------------------------
    | 表單送出驗證
    |--------------------------------------------------------------------------
    */

    async function uploadFiles(){


        const files = fileInput.files;

        if (selectedFiles.length > 3 ) {

            event.preventDefault();

            alert('最多上傳三張圖片');
        }

        for (let i = 0; i < files.length; i++) {

            await uploadSingleFile(files[i], i + 1);
        }
    }


    // 上傳多張圖片至 google drive
    async function uploadSingleFile(file, index){



    /*  進度條 UI */
    const progressHTML = `
        <div class="progress-box" id="box-${index}">
            <div>
                第 ${index} 張：
                <span id="text-${index}">
                    0%
                </span>
            </div>

            <div class="progress-bar">

                <div class="progress-inner"
                     id="progress-${index}">
                </div>

            </div>
        </div>
    `;

    document
        .getElementById('progressContainer')
        .innerHTML += progressHTML;

    /*
    |--------------------------------------------------------------------------
    | FormData
    |--------------------------------------------------------------------------
    */

    const formData = new FormData();

    formData.append('photo', file);

    try {

        const response = await axios.post(
            '{{ route("profile.GoogleUploadImage") }}',
            formData,
            {
                headers: {
                    'X-CSRF-TOKEN':
                        document.querySelector(
                            'meta[name="csrf-token"]'
                        ).content,

                    'Content-Type': 'multipart/form-data'
                },


                /*  進度條 UI */
                onUploadProgress: function(progressEvent) {

                    const percent = Math.round(
                        (progressEvent.loaded * 100)
                        / progressEvent.total
                    );

     
                    document.getElementById(
                        `progress-${index}`
                    ).style.width = percent + '%';

                    document.getElementById(
                        `text-${index}`
                    ).innerText = percent + '%';
                }
            }
        );
        document.getElementById(
            `text-${index}`
        ).innerText = '上傳成功';

    } catch(error) {

        console.log( error )

        document.getElementById(
            `text-${index}`
        ).innerText = '上傳失敗';
    }
    }

</script>

<style>
    body {
        font-family: Arial, sans-serif;
        background: #f4f6f9;
        padding: 30px;
    }

    .container {
        width: 800px;
        margin: auto;
        background: #fff;
        padding: 30px;
        border-radius: 10px;
        box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
    }

    h2 {
        text-align: center;
        margin-bottom: 25px;
    }

    .drop-zone {
        border: 3px dashed #999;
        border-radius: 10px;
        padding: 50px;
        text-align: center;
        cursor: pointer;
        transition: 0.3s;
        background: #fafafa;
    }

    .drop-zone.dragover {
        background: #e3f2fd;
        border-color: #2196f3;
    }

    .drop-zone p {
        margin: 0;
        font-size: 18px;
        color: #666;
    }

    input[type="file"] {
        display: none;
    }

    .preview-container {
        display: flex;
        flex-wrap: wrap;
        gap: 15px;
        margin-top: 25px;
    }

    .preview-item {
        position: relative;
    }

    .preview-image {
        width: 160px;
        height: 160px;
        object-fit: cover;
        border-radius: 8px;
        border: 1px solid #ccc;
    }

    .remove-btn {
        position: absolute;
        top: -8px;
        right: -8px;
        background: red;
        color: white;
        border: none;
        border-radius: 50%;
        width: 25px;
        height: 25px;
        cursor: pointer;
    }

    .submit-btn {
        width: 100%;
        padding: 12px;
        margin-top: 30px;
        border: none;
        background: #007bff;
        color: white;
        font-size: 16px;
        border-radius: 6px;
        cursor: pointer;
    }

    .submit-btn:hover {
        background: #0056b3;
    }

    .error {
        color: red;
        margin-top: 15px;
    }

    .success {
        color: green;
        margin-bottom: 15px;
    }


    .progress-box {
        margin-top: 20px;
    }

    .progress-bar {
        width: 300px;
        height: 25px;
        border: 1px solid #ccc;
        margin-bottom: 15px;
        position: relative;
    }

    .progress-inner {
        height: 100%;
        width: 0%;
        background: green;
        transition: 0.2s;
    }
</style>