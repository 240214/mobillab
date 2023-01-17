<?php

$contacts_n_search = get_field('contacts_n_search', 'option');
$donation_button = get_field('donation_button', 'option');

$phone_number = $contacts_n_search['phone_number'];
$phone_icon = $contacts_n_search['phone_icon'];
$search_icon = $contacts_n_search['search_icon'];

$btn_title = $donation_button['title'];
$btn_link = $donation_button['link'];
?>

<div class="header-content f_2 text-right">
	<div class="dib vam text-left hidden-xs">
		<form class="search" action="" method="get">
			<img src="<?=$search_icon;?>" class="icon">
			<input type="text" name="search" value="" placeholder="Search">
		</form>
		<div class="contacts">
			<img src="<?=$phone_icon;?>" class="icon">
			<a class="phone-number" href="tel:<?=str_replace(' ', '', $phone_number);?>"><?=$phone_number;?></a>
		</div>
	</div>
	<div class="dib vam">
		<a href="<?=$btn_link;?>" class="button orange donation"><?=$btn_title;?></a>
	</div>
</div>