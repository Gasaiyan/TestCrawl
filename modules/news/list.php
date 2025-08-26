<?php
if (!defined('_TAI')) {
    die('Truy cập không hợp lệ');
}
$data = [
    'title' => 'Trang chủ'
];

$filter = filterData();
$keyword = $filter['keyword'] ?? '';
$category = $filter['category'] ?? '';
?>
<!DOCTYPE html>
<html lang="vi">

<head>
    <meta charset="UTF-8">
    <title><?= $data['title'] ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
    body {
        background: #f5f5f5;
        font-family: Arial, sans-serif;
    }

    .container {
        max-width: 1200px;
        margin: auto;
        background: #fff;
        padding: 15px;
        box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
    }

    /* Tin nổi bật */
    .top-news img {
        width: 100%;
        height: auto;
        border-radius: 8px;
    }

    .top-news h2 {
        font-size: 24px;
        font-weight: bold;
        margin-top: 10px;
    }

    /* Tin chính bên trái */
    .news-item {
        display: flex;
        gap: 15px;
        border-bottom: 1px solid #eee;
        padding: 10px 0;
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

    /* Cột phải */
    .side-news .news-item {
        display: flex;
        gap: 10px;
        border-bottom: 1px solid #eee;
        padding: 8px 0;
    }

    .side-news img {
        width: 80px;
        height: 60px;
        object-fit: cover;
        border-radius: 4px;
    }

    .side-news h5 {
        font-size: 15px;
        margin: 0;
    }

    /* Loading */
    #loading {
        text-align: center;
        padding: 10px;
        display: none;
        color: #555;
    }

    /* Suggestion box */
    #suggestionBox {
        position: absolute;
        top: 100%;
        left: 0;
        right: 0;
        background: white;
        border: 1px solid #ccc;
        z-index: 2000;
        display: none;
        max-height: 200px;
        overflow-y: auto;
    }
    #suggestionBox div {
        padding: 8px;
        cursor: pointer;
    }
    #suggestionBox div:hover {
        background: #f0f0f0;
    }
    </style>
</head>

<body>
    <div class="container mt-3 mb-3">

        <!-- Category + Search -->
        <div class="sticky-top bg-white shadow-sm p-2 mb-3" style="z-index: 1000;">
            <!-- Category Tabs -->
            <div class="mb-2">
                <ul class="nav nav-pills justify-content-center flex-wrap gap-2">
                    <li class="nav-item">
                        <a class="nav-link <?= $category == '' ? 'active' : '' ?>"
                            href="index.php?module=news&action=list&category=">Tất cả</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link <?= $category == 'doi-song' ? 'active' : '' ?>"
                            href="index.php?module=news&action=list&category=doi-song">Đời sống</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link <?= $category == 'kinh-doanh' ? 'active' : '' ?>"
                            href="index.php?module=news&action=list&category=kinh-doanh">Kinh doanh</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link <?= $category == 'giao-duc' ? 'active' : '' ?>"
                            href="index.php?module=news&action=list&category=giao-duc">Giáo dục</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link <?= $category == 'the-gioi' ? 'active' : '' ?>"
                            href="index.php?module=news&action=list&category=the-gioi">Thế giới</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link <?= $category == 'phap-luat' ? 'active' : '' ?>"
                            href="index.php?module=news&action=list&category=phap-luat">Pháp luật</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link <?= $category == 'thoi-su' ? 'active' : '' ?>"
                            href="index.php?module=news&action=list&category=thoi-su">Thời sự</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link <?= $category == 'giai-tri' ? 'active' : '' ?>"
                            href="index.php?module=news&action=list&category=giai-tri">Giải trí</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link <?= $category == 'suc-khoe' ? 'active' : '' ?>"
                            href="index.php?module=news&action=list&category=suc-khoe">Sức khỏe</a>
                    </li>
                </ul>
            </div>

            <!-- Search Form -->
            <form action="index.php" method="get" class="row justify-content-center g-2" style="position: relative;">
                <input type="hidden" name="module" value="news">
                <input type="hidden" name="action" value="list">

                <div class="col-md-6 col-sm-8" style="position: relative;">
                    <input type="text" id="searchBox" name="keyword"
                        value="<?= htmlspecialchars($keyword) ?>" class="form-control"
                        placeholder="Tìm kiếm tin tức..." autocomplete="off" onkeyup="suggestSearch()">
                    <div id="suggestionBox"></div>
                </div>
                <div class="col-md-2 col-sm-4 d-grid">
                    <button class="btn btn-primary">Tìm kiếm</button>
                </div>
            </form>
        </div>

        <!-- Tin nổi bật -->
        <div class="top-news mb-4" id="topNews"></div>

        <div class="row">
            <!-- Cột trái -->
            <div class="col-md-8">
                <div id="newsList"></div>
            </div>

            <!-- Cột phải -->
            <div class="col-md-4">
                <div class="side-news" id="sideNews"></div>
            </div>
        </div>

        <div id="loading">Đang tải...</div>
    </div>

    <script>
    let page = 1;
    let loading = false;
    let isFirstLoad = true;

    const params = new URLSearchParams(window.location.search);
    const keyword = params.get("keyword") || "";
    const category = params.get("category") || "";

    async function loadNews() {
        if (loading) return;
        loading = true;
        document.getElementById("loading").style.display = "block";
        document.getElementById("loading").innerText = "Đang tải...";

        const res = await fetch(
            "modules/news/load_news.php?page=" + page +
            "&keyword=" + encodeURIComponent(keyword) +
            "&category=" + encodeURIComponent(category)
        );

        const data = await res.json();

        if (data.length > 0) {
            const newsList = document.getElementById("newsList");
            const sideNews = document.getElementById("sideNews");
            const topNews = document.getElementById("topNews");

            data.forEach((item, index) => {
                if (isFirstLoad && index === 0) {
                    topNews.innerHTML = `
                        <a href="${item.link}" target="_blank">
                            <img src="${item.image}" alt="${item.title}">
                        </a>
                        <h2><a href="${item.link}" target="_blank">${item.title}</a></h2>
                    `;
                } else {
                    const article = document.createElement("article");
                    article.classList.add("news-item");

                    if ((index + page) % 2 === 0) {
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
                        newsList.appendChild(article);
                    } else {
                        article.innerHTML = `
                            <a href="${item.link}" target="_blank">
                                <img src="${item.image}" alt="${item.title}" loading="lazy">
                            </a>
                            <div>
                                <h5><a href="${item.link}" target="_blank">${item.title}</a></h5>
                            </div>
                        `;
                        sideNews.appendChild(article);
                    }
                }
            });

            isFirstLoad = false;
            page++;
            document.getElementById("loading").innerText = "Kéo xuống để tải thêm...";
        } else {
            observer.disconnect();
            document.getElementById("loading").innerText = "Hết tin tức";
        }

        loading = false;
    }

    async function suggestSearch() {
        const query = document.getElementById("searchBox").value.trim();
        const suggestionBox = document.getElementById("suggestionBox");

        if (query.length < 2) {
            suggestionBox.style.display = "none";
            return;
        }

        // --- Gọi suggest.php (database) ---
        const formData = new FormData();
        formData.append("query", query);

        let suggestions = [];
        try {
            const res = await fetch("modules/news/API_suggestSearch.php", { method: "POST", body: formData });
            const data = await res.json();
            if (data.suggestions && data.suggestions.length > 0) {
                suggestions = data.suggestions;
            }
        } catch (e) {
            console.error("API_suggestSearch.php error:", e);
        }

        if (suggestions.length === 0) {
            const formAI = new FormData();
            formAI.append("action", "suggest");
            formAI.append("query", query);

            try {
                const resAI = await fetch("modules/news/API_keyGemini.php", { method: "POST", body: formAI });
                const dataAI = await resAI.json();
                if (dataAI.suggestions && dataAI.suggestions.length > 0) {
                    suggestions = dataAI.suggestions;
                }
            } catch (e) {
                console.error("AI suggest error:", e);
            }
        }

        if (suggestions.length > 0) {
            suggestionBox.innerHTML = "";
            suggestions.forEach(s => {
                const div = document.createElement("div");
                div.textContent = s.trim(); // loại bỏ khoảng trắng thừa
                div.onclick = () => {
                    window.location.href =
                        "index.php?module=news&action=list&keyword=" + encodeURIComponent(s.trim());
                };
                suggestionBox.appendChild(div);
            });
            suggestionBox.style.display = "block";
        } else {
            suggestionBox.style.display = "none";
        }
    }

    const observer = new IntersectionObserver(entries => {
        if (entries[0].isIntersecting) {
            loadNews();
        }
    }, { rootMargin: "0px 0px 200px 0px", threshold: 0.1 });

    observer.observe(document.getElementById("loading"));
    loadNews();
    </script>
</body>
</html>
