<style type="text/css">
    .rs-slide {background: transparent url(<?=$atts['bg_image'];?>) no-repeat center center; background-size: cover; overflow: hidden; height: 100%; width: 100%; z-index: 20; position: relative; display: flex; justify-content: center;}
    .rs-slide .rs-wrap {display: flex; flex-flow: row nowrap; align-items: center; max-width: 890px; margin-left: 20%; line-height: normal; text-align: left;}
    .rs-slide .image {flex: 0 0 auto;}
    .rs-slide .image img {height: 100%; width: 490px;}
    .rs-slide .title,
    .rs-slide .action {margin-top: 20px;}
    .rs-slide .content {font-weight: 300; font-size: 28px;}
    .rs-slide .title {font-weight: bold; font-size: 38px; line-height: 1;}
    .rs-slide .action {display: inline-block; background-color: rgb(163, 209, 51); font-family: Roboto; cursor: pointer; outline: none; font-weight: 500; font-size: 22px; border-radius: 10px; padding: 14px 28px; color: rgb(51, 62, 72); text-decoration: none; white-space: nowrap; width: auto; height: auto;}
    @media all and (max-width: 1366px){
        .rs-slide .rs-wrap {max-width: 770px; margin-left: 30%;}
        .rs-slide .image img {width: 400px;}
        .rs-slide .title {font-size: 30px;}
        .rs-slide .content {font-size: 22px;}
        .rs-slide .action {font-size: 18px; padding: 10px 22px;}
    }
    @media all and (max-width: 1199px){
        .rs-slide .rs-wrap {margin-left: 30px;}
    }
</style>
<div class="rs-slide">
    <div class="rs-wrap">

        <div class="info">
            <div class="content"><?=$atts['content'];?></div>

            <div class="title"><?=$atts['title'];?></div>

            <a class="action" href="<?=$atts['button_link'];?>" rel="nofollow"><?=$atts['button_text'];?></a>
        </div>

        <div class="image">
            <img src="<?=$atts['image'];?>" width="490" height="415">
        </div>

    </div>
</div>
