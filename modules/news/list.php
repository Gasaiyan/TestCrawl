<?php
if(!defined('_TAI')) {
    die('Truy cập không hợp lệ');
}

$data = [
    'title' => 'Trang chủ'
];

//Xử lý phân trang
$maxData = getRows("SELECT id FROM crawl_news"); //Tổng dữ liệu
$perPage = 10 ; //Số dòng dữ liệu một trang
$maxPage=ceil($maxData/$perPage);
$offset = 0;
$page = 1;
//get page 
if(isset($filter['page'])){
    $page = $filter['page'];
}

if($page > $maxPage || $page < 1){
    $page = 1;
}

    $offset = ($page -1) * $perPage;

// Lấy dữ liệu tin tức
$sql = "SELECT * FROM crawl_news ORDER BY pubDate DESC LIMIT $perPage OFFSET $offset";
$listNews = getAll($sql);


?>

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

.pagination {
    margin: 20px;
    text-align: center;
}

.pagination a {
    display: inline-block;
    padding: 6px 12px;
    margin: 0 4px;
    border: 1px solid #ddd;
    border-radius: 4px;
    text-decoration: none;
    color: #333;
}

.pagination a.active {
    background: #d33;
    color: #fff;
    border-color: #d33;
}
</style>

<div class="news-list">
    <?php
if(!empty($listNews)){
    foreach($listNews as $item){
?>
    <article class="news-item">
        <div class="news-image">
            <a href="<?= $item['link'] ?>" target="_blank">
                <img src="<?= $item['image'] ?>" alt="<?= htmlspecialchars($item['title']) ?>">
            </a>
        </div>
        <div class="news-info">
            <h4>
                <a href="<?= $item['link'] ?>" target="_blank">
                    <?= $item['title'] ?>
                </a>
            </h4>
            <div>
                <span class="source"><?= $item['source'] ?></span>
                <span class="time-rel"><?= date("d/m/Y H:i", strtotime($item['pubDate'])) ?></span>
            </div>
        </div>
    </article>
    <?php
    }
}else{
    echo "<p>Không có dữ liệu.</p>";
}
?>
</div>

<!-- Phân trang -->
<nav aria-label="Page navigation example">
    <ul class="pagination">
        <!-- Xử lý nút 'Trước' -->

        <?php 
            if($page>1):
            ?>
        <li class="page-item"><a class="page-link"
                href="?<?php echo $queryString?>&page=<?php echo $page-1;?>">Trước</a></li>
        <?php 
            endif;
            ?>
        <!-- Tính vị trí bắt đầu -->

        <?php
                $start = $page - 1;
                if($start<1){
                    $start = 1;
                }
            ?>

        <?php 
            if($start>1):
            ?>
        <li class="page-item"><a class="page-link" href="?<?php echo $queryString?>&page=<?php echo $page-1;?>">...</a>
        </li>
        <?php 
            endif;

            $end = $page + 1;
            if($end>$maxPage){
                $end = $maxPage;
            }
            
            ?>


        <?php for($i = $start; $i<=$end; $i++):?>

        <li class="page-item <?php echo($page == $i) ? 'active': false?>"><a class="page-link"
                href="?<?php echo $queryString?>&page=<?php echo $i; ?>"><?php echo $i;?></a>
        </li>

        <?php 
            endfor;
                if($end < $maxPage):
            ?>
        <li class="page-item"><a class="page-link" href="?<?php echo $queryString?>&page=<?php echo $page+1;?>">...</a>
        </li>

        <?php endif; ?>

        <!-- Xử lý nút 'Sau' -->


        <?php 
            if($page<$maxPage):
            ?>
        <li class="page-item"><a class="page-link" href="?<?php echo $queryString?>&page=<?php echo $page+1;?>">Sau</a>
        </li>
        <?php 
            endif;
            ?>
    </ul>
</nav>