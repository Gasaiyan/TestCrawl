<?php
if(!defined('_TAI')) {
    die('Truy cập không hợp lệ');
}
$data = [
    'title' => 'Trang chủ'
];
?>
<!DOCTYPE html>
<html lang="vi">

<head>
    <meta charset="UTF-8">
    <title><?= $data['title'] ?></title>
    <style>
    .news-list {
        display: flex;
        flex-direction: column;
        gap: 15px;
        margin: 20px;
    }

    .news-item {
        display: flex;
        gap: 15px;
        border-bottom: 1px solid #ddd;
        padding-bottom: 10px;
    }

    .news-item img {
        width: 160px;
        height: 100px;
        object-fit: cover;
        border-radius: 5px;
    }

    .news-info h4 {
        margin: 0;
        font-size: 18px;
    }

    .news-info h4 a {
        color: #333;
        text-decoration: none;
    }

    .news-info h4 a:hover {
        color: #d33;
    }

    .news-info .source {
        font-size: 13px;
        color: #666;
        margin-right: 10px;
    }

    .news-info .time-rel {
        font-size: 13px;
        color: #999;
    }

    #loading {
        text-align: center;
        padding: 10px;
        display: none;
        color: #555;
    }
    </style>
</head>

<body>
    <div class="news-list" id="newsList"></div>
    <div id="loading">Đang tải...</div>

    <script>
    let page = 1;
    let loading = false;

    async function loadNews() {
        if (loading) return;
        loading = true;
        document.getElementById("loading").style.display = "block";

        const res = await fetch("modules/news/load_news.php?page=" + page);

        const data = await res.json();

        if (data.length > 0) {
            const container = document.getElementById("newsList");
            data.forEach(item => {
                const article = document.createElement("article");
                article.classList.add("news-item");
                article.innerHTML = `
                    <div class="news-image">
                        <a href="${item.link}" target="_blank">
                            <img src="${item.image}" alt="${item.title}" loading="lazy">
                        </a>
                    </div>
                    <div class="news-info">
                        <h4><a href="${item.link}" target="_blank">${item.title}</a></h4>
                        <div>
                            <span class="source">${item.source}</span>
                            <span class="time-rel">${item.pubDate}</span>
                        </div>
                    </div>
                `;
                container.appendChild(article);
            });
            page++;
        } else {
            // Nếu hết dữ liệu thì bỏ observer
            observer.disconnect();
            document.getElementById("loading").innerText = "Hết tin tức";
        }

        if (data.length > 0) {
            // ... append dữ liệu
            page++;
            document.getElementById("loading").style.display = "block"; // luôn giữ loading hiển thị
            document.getElementById("loading").innerText = "Kéo xuống để tải thêm...";
        } else {
            observer.disconnect();
            document.getElementById("loading").innerText = "Hết tin tức";
        }
        loading = false;

    }

    // Tự động load khi tới cuối trang
    const observer = new IntersectionObserver(entries => {
        if (entries[0].isIntersecting) {
            loadNews();
        }
    }, {
        rootMargin: "0px 0px 200px 0px",
        threshold: 0.1
    });

    observer.observe(document.getElementById("loading"));

    // Load lần đầu
    loadNews();
    </script>
</body>

</html>