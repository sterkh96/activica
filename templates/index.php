<!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-EVSTQN3/azprG1Anm3QDgpJLIm9Nao0Yz1ztcQTwFspd3yD65VohhpuuCOmLASjC"
          crossorigin="anonymous">
    <title>Document</title>
</head>
<body>
<header>
    <!-- As a link -->
    <nav class="navbar navbar-light bg-light">
        <div class="container">
            <a class="navbar-brand" href="/"><?= $title; ?></a>
        </div>
    </nav>
</header>
<div class="container">
    <div class="breadcrumbs my-4">
        <a href="/">Магазин</a>
        <?php if (isset($_GET['category'])) {
            $cat = $cats[$_GET['category']] ?? NULL;
            if ($cat) {
                $parent = $cats[$cat['parent_id']] ?? NULL;
                if ($parent) {
                    echo "<span> > </span><a href=\"?category=$parent[id]\">$parent[title]</a>";
                }
                echo "<span> > </span><a href=\"?category=$cat[id]\">$cat[title]</a>";
            }


        }
        ?>
    </div>
    <div class="vendors mb-5">
        <form action="/" method="GET" class="mt-4">
            <?php if(isset($_GET['category'])): ?>
                <input type="hidden" name="category" value="<?= $_GET['category']; ?>">
            <?php endif; ?>
            <div class="filter">
                <p class="filter__title mb-1"><b>Производитель</b></p>
                <div class="d-flex">
                    <?php foreach ($vendors as $vendor): ?>
                        <?php $attrs = [
                            'checked' => (isset($_GET['vendor']) && in_array($vendor['id'], $_GET['vendor'])) ? 'checked' : '',
                        ]; ?>
                        <div class="me-4">
                            <input type="checkbox" name="vendor[]" class="" id="<?= 'vendor_' . $vendor['id']; ?>" autocomplete="off" value="<?= $vendor['id']; ?>" <?= implode(' ', $attrs) ?>>
                            <label class="" for="<?= 'vendor_' . $vendor['id']; ?>"><?= $vendor['name']; ?></label>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>
            <button class="btn btn-primary no-js mt-3">Применить</button>
        </form>

    </div>
    <?php if (!empty($products)): ?>
        <table class="table table-striped">
            <thead>
            <?php
            $headings = array(
                'name' => 'Название',
                'price' => 'Цена',
                'optprice' => 'Цена&nbsp;(опт)',
                'picture' => 'Изображение',
                'article' => 'Артикул',
                'category_id' => 'Категория',
                'description' => 'Описание',
                'available' => 'Наличие',
                'status_new' => 'Новинка',
                'status_action' => 'Акция',
                'status_top' => 'ТОП',
                'vendor' => 'Производитель'
            ); ?>
            <tr>
                <?php foreach (array_keys($products[0]) as $heading): ?>
                    <?php if ($heading === 'url' || $heading === 'id') continue; ?>
                    <th><?= $headings[$heading]; ?></th>
                <?php endforeach; ?>
            </tr>
            </thead>
            <tbody>

            <?php foreach ($products as $index => $p): ?>
                <tr>
                    <?php foreach ($p as $key => $field): ?>
                        <?php if ($key === 'url' || $key === 'id') continue; ?>
                        <td style="vertical-align: middle;">
                            <?php if ($key === 'picture'): ?>
                                <img src="<?= $field; ?>" alt="<?= $products[$index]['name']; ?>" width="75" height="75">
                            <?php elseif ($key === 'category_id'): ?>
                                <a href="?category=<?= $cats[$field]['parent_id']; ?>"><?= $cats[$cats[$field]['parent_id']]['title']; ?></a>
                                <span> > </span>
                                <a href="?category=<?= $field; ?>"><?= $cats[$field]['title']; ?></a>
                            <?php else: ?>
                                <span><?= $field === 1 ? 'Да' : ($field === 0 ? 'Нет' : $field); ?></span>
                                <?php if($key === 'name' && isset($props[$p['id']])): ?>
                                <small class="d-block text-secondary"><?= implode('; ', $props[$p['id']]); ?></small>
                                <?php endif; ?>
                            <?php endif; ?>
                        </td>
                    <?php endforeach; ?>
                </tr>
            <?php endforeach; ?>
            </tbody>

        </table>
    <?php else : ?>
        <div class="alert alert-warning">К сожалению, тут пусто:(</div>
    <?php endif; ?>
</div>

<script src="/assets/js/main.js"></script>
</body>
</html>