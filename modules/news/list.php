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

    /* Tab category đẹp hơn */
    .nav-pills .nav-link {
        border-radius: 20px;
        padding: 6px 15px;
        font-size: 14px;
    }

    .nav-pills .nav-link.active {
        background-color: #d33;
    }
    </style>
</head>

<body>
    <div class="container mt-3 mb-3">

        <!-- Category Tabs -->
        <div class="mb-3">
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
        <form action="index.php" method="get" class="row justify-content-center g-2">
            <input type="hidden" name="module" value="news">
            <input type="hidden" name="action" value="list">

            <div class="col-md-6 col-sm-8">
                <input type="text" name="keyword" value="<?= htmlspecialchars($keyword) ?>" class="form-control"
                    placeholder="Tìm kiếm tin tức...">
            </div>
            <div class="col-md-2 col-sm-4 d-grid">
                <button class="btn btn-primary">Tìm kiếm</button>
            </div>
        </form>

    </div>

    <div class="news-list" id="newsList"></div>
    <div id="loading">Đang tải...</div>

    <script>
    let page = 1;
    let loading = false;

    // Lấy keyword & category từ URL
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