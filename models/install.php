<?php

if (!isset($_POST['step'])) {
    $res[] = '<form method="POST">   
<input type="hidden" name="step" value="2">
<label>Введите ID документа который будет родителем для каталога продукции:<input type="text" name="input" value="1"></label> 
<input type="submit" value="далее">
</form>';
}

if ($_POST['input'] && is_numeric($_POST['input'])) {
    $this->setConfig(array('start_page' => $_POST['input']));
    $this->saveConfig();
    $res[] = 'Точка входа установлена';
}


if ($_POST['step'] == 2 || $_POST['step'] == 3 && $_POST['ok'] != 1) {
    $res[] = 'В фаил .htaccess вставте строку: <br>RewriteRule ^<i>catalog</i>/([^/]+)?$ index.php?q=' . $_POST['input'] . '&mshop_id=$1 [L,QSA] <i><br>Вставлять перед строкой: RewriteRule ^(.*)$ index.php?q=$1 [L,QSA]</i>
     <form method="POST">   
<input type="hidden" name="step" value="3">
<label><input type="checkbox" name="ok" value="1" checked> Строка вставлена, <b>я понимаю что без этой строчки ничего не будет работать</b>.</label> <br>
<input type="submit" value="далее">
</form>';
}

if ($_POST['step'] == 3 && $_POST['ok'] == 1) {
    $res[] = '<form method="POST">    
<input type="hidden" name="step" value="3">
<input type="hidden" name="ok" value="1">
<label><input type="checkbox" name="install" value="1" checked> Установить БД и все необходимые чанки</label> <br>
<label><input type="checkbox" name="template" value="1" checked> Установить 2 новых шаблона (товар, категория)</label> <br>
<label><input type="checkbox" name="demo" value="1" checked> Установить демо товары (доступно только с генерацией новых шаблонов)</label> <br>
<label><input type="checkbox" name="cart" value="1" checked> Создать новый документ корзина</label>

<input type="submit" value="установить">
</form>';




    if ($_POST['install'] == 1) {

        $sql = 'CREATE TABLE IF NOT EXISTS `[+prefix+]mshop_brand` (
  `id_content` int(11) NOT NULL,
  `id_brand` int(11) NOT NULL,
  PRIMARY KEY (`id_content`),
  KEY `id_brand` (`id_brand`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;';
        $sql = str_replace('[+prefix+]', $this->modx->db->config['table_prefix'], $sql);
        $result = $this->modx->db->query($sql);
        if ($result)
            $res[] = 'Таблица mshop_brand установленна';

        $sql = "CREATE TABLE IF NOT EXISTS `[+prefix+]mshop_content` (
  `id` int(10) NOT NULL AUTO_INCREMENT,
  `pagetitle` varchar(255) NOT NULL DEFAULT '',
  `longtitle` varchar(255) NOT NULL DEFAULT '',
  `description` varchar(255) NOT NULL DEFAULT '',
  `alias` varchar(255) DEFAULT '',
  `published` int(1) NOT NULL DEFAULT '0',
  `pub_date` int(20) NOT NULL DEFAULT '0',
  `parent` int(10) NOT NULL DEFAULT '0',
  `isfolder` int(1) NOT NULL DEFAULT '0',
  `introtext` text COMMENT 'Used to provide quick summary of the document',
  `content` mediumtext,
  `template` int(10) NOT NULL DEFAULT '1',
  `menuindex` int(10) NOT NULL DEFAULT '0',
  `menutitle` varchar(255) NOT NULL DEFAULT '' COMMENT 'Menu title',
  `hidemenu` tinyint(1) NOT NULL DEFAULT '0' COMMENT 'Hide document from menu',
  `richtext` tinyint(1) NOT NULL DEFAULT '1',
  `editedon` bigint(20) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `id` (`id`),
  KEY `parent` (`parent`),
  KEY `aliasidx` (`alias`),
  FULLTEXT KEY `content_ft_idx` (`pagetitle`,`description`,`content`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='Contains the site document tree.' AUTO_INCREMENT=1 ;";
        $sql = str_replace('[+prefix+]', $this->modx->db->config['table_prefix'], $sql);
        $result = $this->modx->db->query($sql);
        if ($result)
            $res[] = 'Таблица mshop_content установленна.';


        $sql = 'CREATE TABLE IF NOT EXISTS `[+prefix+]mshop_external_ids` (
  `id_content` int(11) NOT NULL,
  `id_external` varchar(255) NOT NULL,
  PRIMARY KEY (`id_content`,`id_external`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;';
        $sql = str_replace('[+prefix+]', $this->modx->db->config['table_prefix'], $sql);
        $result = $this->modx->db->query($sql);
        if ($result)
            $res[] = 'Таблица соответствий для 1с установленна (mshop_external_ids)';


        $sql = "CREATE TABLE IF NOT EXISTS `[+prefix+]mshop_orders` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `id_delivery` int(11) DEFAULT NULL,
  `delivery_price` float(10,2) NOT NULL DEFAULT '0.00',
  `id_payment` int(11) DEFAULT NULL,
  `payment_status` int(11) NOT NULL DEFAULT '0',
  `payment_date` datetime NOT NULL,
  `create_date` datetime DEFAULT NULL,
  `id_user` int(11) NOT NULL DEFAULT '0',
  `name` varchar(255) NOT NULL DEFAULT '',
  `user_details` text NOT NULL,
  `phone` varchar(255) NOT NULL DEFAULT '',
  `email` varchar(255) NOT NULL,
  `address` varchar(255) NOT NULL,
  `comment` varchar(1024) NOT NULL,
  `status` int(11) NOT NULL DEFAULT '0',
  `tracking_num` varchar(255) DEFAULT NULL,
  `products_details` text NOT NULL,
  `ip` varchar(15) NOT NULL,
  `send_date` datetime DEFAULT NULL,
  `edit_date` datetime DEFAULT NULL,
  `recall_date` datetime DEFAULT NULL,
  `currency` varchar(15) NOT NULL,
  `price` double NOT NULL,
  `access_key` varchar(255) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `login` (`id_user`),
  KEY `date` (`create_date`),
  KEY `status` (`status`),
  KEY `code` (`tracking_num`),
  KEY `payment_status` (`payment_status`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=1";
        $sql = str_replace('[+prefix+]', $this->modx->db->config['table_prefix'], $sql);
        $result = $this->modx->db->query($sql);
        if ($result)
            $res[] = 'Таблица для работы с заказами установленна (mshop_order)';


        $sql = "CREATE TABLE IF NOT EXISTS `[+prefix+]mshop_properties` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  `in_product` int(1) DEFAULT NULL,
  `in_filter` int(1) DEFAULT NULL,
  `in_compare` int(1) DEFAULT NULL,
  `enabled` int(1) NOT NULL DEFAULT '1',
  `options` text NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;";
        $sql = str_replace('[+prefix+]', $this->modx->db->config['table_prefix'], $sql);
        $result = $this->modx->db->query($sql);
        if ($result)
            $res[] = 'Таблица дополнительных свойств установленна (mshop_properties)';

        $sql = "CREATE TABLE IF NOT EXISTS `[+prefix+]mshop_properties2cat` (
  `id_property` int(11) NOT NULL,
  `id_content` int(11) NOT NULL,
  PRIMARY KEY (`id_property`,`id_content`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;";
        $sql = str_replace('[+prefix+]', $this->modx->db->config['table_prefix'], $sql);
        $result = $this->modx->db->query($sql);
        if ($result)
            $res[] = 'Таблица соответствия свойств и категорий установленна (mshop_properties2cat)';


        $sql = "CREATE TABLE IF NOT EXISTS `[+prefix+]mshop_properties_values` (
  `id_content` int(11) NOT NULL,
  `id_property` int(11) NOT NULL,
  `value` varchar(512) NOT NULL,
  PRIMARY KEY (`id_content`,`id_property`),
  KEY `value` (`value`(333))
) ENGINE=MyISAM DEFAULT CHARSET=utf8;";
        $sql = str_replace('[+prefix+]', $this->modx->db->config['table_prefix'], $sql);
        $result = $this->modx->db->query($sql);
        if ($result)
            $res[] = 'Таблица содержащая значения свойств установленна (mshop_properties_values)';

        $sql = "CREATE TABLE IF NOT EXISTS `[+prefix+]mshop_tmplvar_contentvalues` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `tmplvarid` int(10) NOT NULL DEFAULT '0' COMMENT 'Template Variable id',
  `contentid` int(10) NOT NULL DEFAULT '0' COMMENT 'Site Content Id',
  `value` text,
  PRIMARY KEY (`id`),
  KEY `idx_tmplvarid` (`tmplvarid`),
  KEY `idx_id` (`contentid`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;";
        $sql = str_replace('[+prefix+]', $this->modx->db->config['table_prefix'], $sql);
        $result = $this->modx->db->query($sql);
        if ($result)
            $res[] = 'Таблица содержащая TV параметры документов установленна (mshop_tmplvar_contentvalues)';

        $sql = "CREATE TABLE IF NOT EXISTS `[+prefix+]mshop_variant` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `id_content` int(11) NOT NULL,
  `article` varchar(255) NOT NULL,
  `name` varchar(255) NOT NULL,
  `price` float(10,2) NOT NULL,
  `stock` int(11) NOT NULL,
  `position` int(11) NOT NULL,
  `unit` varchar(55) NOT NULL,
  `id_external` int(11) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;";
        $sql = str_replace('[+prefix+]', $this->modx->db->config['table_prefix'], $sql);
        $result = $this->modx->db->query($sql);
        if ($result)
            $res[] = 'Таблица вариантов установленна  (mshop_variant)';

        $sql = "INSERT INTO " . $this->modx->getFullTableName('site_snippets') . "
            (`name`, `description`, `editor_type`, `category`, `cache_type`, `snippet`, `locked`, `properties`, `moduleguid`) 
            VALUES ('MShopCart', 'Вывод корзины на фронт', 0, 0, 0, '\r\nrequire MODX_BASE_PATH.\"assets/modules/shop/MShopCart.php\";\r\n', 0, '', ' ');";
        $result = $this->modx->db->query($sql);
        if ($result)
            $res[] = 'Снипет MShopCart установлен';
        $sql = "INSERT INTO " . $this->modx->getFullTableName('site_snippets') . "
            (`name`, `description`, `editor_type`, `category`, `cache_type`, `snippet`, `locked`, `properties`, `moduleguid`) 
            VALUES ('MShopCatalog', 'Вывод товаров на сайт', 0, 0, 0, '\r\nrequire MODX_BASE_PATH.\"assets/modules/shop/MShopCatalog.php\";\r\n', 0, '', ' ');";
        $result = $this->modx->db->query($sql);
        if ($result)
            $res[] = 'Снипет MShopCatalog установлен';
        $sql = "INSERT INTO " . $this->modx->getFullTableName('site_snippets') . "
            (`name`, `description`, `editor_type`, `category`, `cache_type`, `snippet`, `locked`, `properties`, `moduleguid`) 
            VALUES ('MShopMenu', 'Вывод меню из каталога MShop', 0, 0, 0, '\r\nrequire MODX_BASE_PATH.\"assets/modules/shop/MShopMenu.php\";\r\n', 0, '', ' ');";
        $result = $this->modx->db->query($sql);
        if ($result)
            $res[] = 'Снипет MShopMenu установлен';

        $sql = "INSERT INTO " . $this->modx->getFullTableName('site_snippets') . "
            (`name`, `description`, `editor_type`, `category`, `cache_type`, `snippet`, `locked`, `properties`, `moduleguid`) 
            VALUES ('MShopOrder', 'Вывод оформленных заказов во фронт', 0, 0, 0, '\r\nrequire MODX_BASE_PATH.\"assets/modules/shop/MShopOrder.php\";\r\n', 0, '', ' ');";
        $result = $this->modx->db->query($sql);
        if ($result)
            $res[] = 'Снипет MShopOrder установлен';

        $sql = "INSERT INTO " . $this->modx->getFullTableName('site_snippets') . "
            (`name`, `description`, `editor_type`, `category`, `cache_type`, `snippet`, `locked`, `properties`, `moduleguid`) 
            VALUES ('MShopBreadcrumbs', 'Вывод хлебных крошек на фронт', 0, 0, 0, '\r\nrequire MODX_BASE_PATH.\"assets/modules/shop/MShopBreadcrumbs.php\";\r\n', 0, '', ' ');";
        $result = $this->modx->db->query($sql);
        if ($result)
            $res[] = 'Снипет MShopBreadcrumbs установлен';

        $sql = "INSERT INTO " . $this->modx->getFullTableName('site_plugins') . " (`name`, `description`, `editor_type`, `category`, `cache_type`, `plugincode`, `locked`, `properties`, `disabled`, `moduleguid`) VALUES
                ('MShop', 'Плагин для функционирования модуля', 0, 0, 0, 'require_once MODX_BASE_PATH.\"assets/modules/shop/plugin.php\";', 0, '', 0, ' ');";
        $result = $this->modx->db->query($sql);
        if ($result) {

            $id_plugin = $this->modx->db->getInsertId();
            $sql = "INSERT INTO " . $this->modx->getFullTableName('site_plugin_events') . " (`pluginid`, `evtid`, `priority`) VALUES
                (" . $id_plugin . ", 28, 0),
                (" . $id_plugin . ", 30, 0),
                (" . $id_plugin . ", 91, 0)
                ;";
            $result = $this->modx->db->query($sql);
            if ($result)
                $res[] = 'Плагин1 установлен';
        }


        $sql = "INSERT INTO " . $this->modx->getFullTableName('site_plugins') . " (`name`, `description`, `editor_type`, `category`, `cache_type`, `plugincode`, `locked`, `properties`, `disabled`, `moduleguid`) VALUES
                ('MShop2', 'Плагин для функционирования модуля', 0, 0, 0, 'require_once MODX_BASE_PATH.\"assets/modules/shop/plugin2.php\";', 0, '', 0, ' ');";
        $result = $this->modx->db->query($sql);
        if ($result) {

            $id_plugin = $this->modx->db->getInsertId();
            $sql = "INSERT INTO " . $this->modx->getFullTableName('site_plugin_events') . " (`pluginid`, `evtid`, `priority`) VALUES
                (" . $id_plugin . ", 29, 0);";
            $result = $this->modx->db->query($sql);
            if ($result)
                $res[] = 'Плагин2 установлен';
        }


        $sql = "INSERT INTO " . $this->modx->getFullTableName('site_htmlsnippets') . " (`name`, `description`, `editor_type`, `category`, `cache_type`, `snippet`, `locked`) VALUES
( 'cart_tpl', 'расширенный код корзины при оформлении заказа', 0, 0, 0, '" . $this->modx->db->escape('<div><h1>Корзина</h1><form class="mshop_cart" action="/assets/modules/shop/ajax.php" method="POST"><input type="hidden" name="MShop_action" value="add"><table><tr><th>Наиименование</th><th>Кол-во</th><th>Цена</th><th></th></tr>[+products_html+]<tr><td colspan="2">На сумму:</td><td colspan="2">[+total+]</td></tr></table>  <input type="submit" value="пересчитать"></form></div>') . "', 0),
( 'products_tpl', 'код для одной строки в корзине', 0, 0, 0, '" .  $this->modx->db->escape('<tr><td><a href="">[+pagetitle+] ([+name+] [+article+])</a> ([+id_variant+])</td><td><input type="text" value="[+count+]" name="MShop_variant[[+id_variant+]]" onChange="addCart(this, [+id_variant+], this.value)"></td><td>[+price+]</td><td><a href="javascript:;" onClick="deleteCart(this, [+id_variant+]);">Удалить</a></td></tr>') . "', 0),
( 'min_cart_tpl', 'код для минимальной корзины', 0, 0, 0, '" .  $this->modx->db->escape('<p><strong class="blue">Корзина</strong></p>[+products_html+]товара на сумму<p><strong class="blue">[+total+] рублей</strong></p><a href="/cart">Оформить заказ</a>') . "', 0),
( 'min_products_tpl', 'код одной строки товара для минимальной корзины', 0, 0, 0, '" .  $this->modx->db->escape('<a href="[+url+]">[+pagetitle+] [+name+]</a> - [+price+] [+count+]шт.<br/><br/>') . "', 0),
( 'catalog_product_tpl', 'шаблон для списка товаров', 0, 0, 0, '" .  $this->modx->db->escape('
<div class="product">
<form class="mshop_product" action="/assets/modules/shop/ajax.php" method="POST">
      <a href="[+url+]" class="product_name">[+pagetitle+] </a>
      <div id="livecart_animate[+id_variant+]" style="display:none;" class="helper">
	  <a href="[+url+]">
	  <img src="[+tv1+]" alt="[+longtitle+]" />
	  </a>
      </div>
      <div class="product_image">
	  <a href="[+url+]">
	  <img src="[+tv1+]" alt="[+longtitle+]" />
	  </a>
      </div>
      <div class="product_desc" >	       	  
	  <p>[+introtext+]</p>      
	  <span class="product_price">[+price+]&nbsp;руб</span> <br />
    <input type="hidden" name="MShop_action" value="add">
    [+variants+]
    <input type="submit" value="купить">	  
      </div>

</form>
    </div>    ') . "', 0),
( 'helper', 'код хелпера', 0, 0, 0, '" .  $this->modx->db->escape('
    <script>
     $(document).ready(function () {
                $(\'#MShopHelper\').hide();
                $(\'.mshop_product\').submit(function(){		
                            $(\'#MShopHelper\').appendTo(this);			
                            $(\'#MShopHelper\').show();
                            return false;			
                    });
	});
    </script>
    <div id="MShopHelper">
	<a onclick="hideHelper();return false;" id="cancelButton" href="javascript:;" title="Закрыть">X</a>	

	<label>Кол-во:<input type="text" size="10" class="quantity" name="count" value="1" id="MShop_count"/></label>
	<a onclick="upHelper();return false;" href="javascript:;" id="up" title="Увеличить кол-во">&uarr;</a>
	<a onclick="downHelper();return false;" id="down" href="javascript:;" title="Уменьшить кол-во">&darr;</a>		

	<button class="bluebutton rounded" onclick="sendHelper(this);return false;">Добавить</button>
</div><!--/mshophelper-->
') . "', 0),
( 'empty_cart_tpl', 'код пустой корзины', 0, 0, 0, '" . '<div><h1>Корзина</h1><p>Корзина пуста</p></div>' . "', 0);";

        $result = $this->modx->db->query($sql);
        if ($result)
            $res[] = 'Чанки cart_tpl,products_tpl,empty_cart_tpl, min_products_tpl, min_cart_tpl, catalog_product_tpl установлены';

        $sql = "INSERT INTO " . $this->modx->getFullTableName('system_eventnames') . "  (
`id` ,`name` ,`service` ,`groupname`)
VALUES (NULL , 'OnMShopOrderFrontView', '7', 'MShop'),
 (NULL , 'OnMShopCartFrontInit', '7', 'MShop'),
 (NULL , 'OnMShopModelInit', '7', 'MShop'),
 (NULL , 'OnMShopControllerRun', '7', 'MShop')
;";
        $result = $this->modx->db->query($sql);
        if ($result)
            $res[] = 'Системные события установлены';
    }

    if ($_POST['template'] == 1) {
        $sql = "INSERT INTO " . $this->modx->getFullTableName('site_templates') . "  (`templatename` ,`description` ,`editor_type`,`category`,`content`)
VALUES ('Товар', 'Шаблон 1 товара','0','0','
<html>
<head>
<script type=\"text/javascript\" src=\"https://ajax.googleapis.com/ajax/libs/jquery/1.4.3/jquery.min.js\"></script>
</head>
<body>
[!MShopCart?cart_tpl=`min_cart_tpl`&products_tpl=`min_products_tpl`!]
<p>[!MShopBreadcrumbs!]</p>	
<h1>[*pagetitle*]</h1>
[*content*]
<a rel=\"colorbox\" href=\"[*blogimage*]\"><img src=\"[*blogimage*]\" alt=\"\"></a>
							
<div class=\"tovprice\">[*price*] P</div>
							
<form class=\"mshop_product\" action=\"/assets/modules/shop/ajax.php\" method=\"POST\">
<button type=\"submit\" class=\"bluebutton rounded\">купить</button>
 <input type=\"hidden\" name=\"MShop_action\" value=\"add\">
[*variants*]
</form>				
</body>
</html>
');";
        $result = $this->modx->db->query($sql);
        $product_template = $this->modx->db->getInsertId();
        if ($result)
            $res[] = 'Шаблон товара установлен ID:' . $product_template;

        $sql = "INSERT INTO " . $this->modx->getFullTableName('site_templates') . "  (`templatename` ,`description` ,`editor_type`,`category`,`content`)
VALUES ('Категория', 'Шаблон для вывода товаров одной категории','0','0','
<html>
<head>
<script type=\"text/javascript\" src=\"https://ajax.googleapis.com/ajax/libs/jquery/1.4.3/jquery.min.js\"></script>
</head>
<body>
[!MShopCart?cart_tpl=`min_cart_tpl`&products_tpl=`min_products_tpl`!]
<p>[!MShopBreadcrumbs!]</p>
			
<h1>[*pagetitle*]</h1>
[*content*]
[!MShopCatalog?tpl=`catalog_product_tpl` &parent=`[*id*]` &depth=`2` &limit=`10`&order=`content.menuindex DESC`!]
</form>				
</body>
</html>
');";
        $result = $this->modx->db->query($sql);
        $category_template = $this->modx->db->getInsertId();
        if ($result)
            $res[] = 'Шаблон категории установлен ID:' . $category_template;




        $this->setConfig(array('category_template' => $category_template, 'product_template' => $product_template));
        $this->saveConfig();
        $res[] = 'Конфиг перезаписан для новых шаблонов';
    }

    if ($_POST['demo'] == 1 && $_POST['template'] == 1) {
        $sql = "INSERT INTO " . $this->modx->getFullTableName('mshop_content') . " (`id`, `pagetitle`, `longtitle`, `description`, `alias`, `published`, `pub_date`, `parent`, `isfolder`, `introtext`, `content`, `template`, `menuindex`, `menutitle`, `hidemenu`, `richtext`, `editedon`) VALUES
(1, 'Весовое оборудование', '', '', '', 1, 0, 0, 1, '', '<p>Весовое оборудование может сильно отличаться в зависимости от области применения и размеров торговой точки.</p>', " . $category_template . ", 14, '', 0, 1, 1349422541),
(2, 'Торговое оборудование', '', '', '', 1, 0, 0, 1, '', '<p>Торговое оборудование, технические средства (машины, автоматические устройства, поточные линии и т.п.), используемые на предприятиях розничной и оптовой торговли, общественного питания, в складах, хранилищах и на базах.</p>', " . $category_template . ", 14, '', 1, 1, 1349422575),
(3, 'Электронные, счетные весы', '', '', 'electro', 1, 0, 1, 0, '', '<p>Электронные торговые весы предназначены для взвешивания товара, фасовки, последующего определения его стоимости по цене за килограмм и общей массе. Преимущество современных весов: определение массы товара с точностью до 1 грамма. Компания SBM предоставляет линейку ведущих отечественных</p>', " . $product_template . ", 15, '1', 0, 1, 1349700353),
(4, 'Контрольные весы', '', '', '', 1, 0, 1, 1, '', '<p><strong>Контрольные весы</strong> - важный атрибут любого магазина. \r\nЖелание не просто торговать, но и не быть обманутым, заставило \r\nзадуматься о простейшей стандартизации мер и весов. Ваши покупатели \r\nимеют право проверки подлинности веса отпущенных ему товаров. Компания \r\nSBM предлагает разместить в доступном для покупателя месте высокоточное \r\nизмерительное оборудование.</p>', " . $product_template . ", 7, '', 1, 1, 1349700407),
(5, 'Порционные весы', '', '', '', 1, 0, 1, 1, '', '<p><strong>Порционные весы</strong> &mdash; предназначены для решения простых \r\nзадач взвешивания. Широко применяются в различной деятельности: баров, \r\nресторанов, кафе, домашнем использовании - для взвешивания товаров \r\nкоторые не имеют большого веса. Компания SBM предлагает весы от лучших \r\nмировых производителей которые имеют длительный период эксплуатации и \r\nвысокопрочность оборудования.</p>', " . $product_template . ", 7, '', 1, 1, 1349700418);
";
        $result = $this->modx->db->query($sql);
        if ($result)
            $res[] = 'Демо товары установлены';

        $sql = "INSERT INTO " . $this->modx->getFullTableName('mshop_variant') . " (`id_content`, `article`, `name`, `price`, `stock`, `position`, `unit`, `id_external`) VALUES
( 3, '3 772', 'Весы CAS AP-1 (6M)', 213.00, 1, 1, '', 0),
( 3, '1 032', 'Весы CAS AP-1 (15M)', 215.00, 2, 1, '', 0),
( 5, '9 681', 'Весы CAS AD-2.5', 186.00, 1, 1, 'шт', 0),
( 5, '17 658', 'Весы CAS AD-5', 188.00, 2, 1, 'шт', 0),
( 4, '45ПА', 'Комплект АПк', 1503.00, 0, 1, '', 0),
( 4, '46ПА', 'Комплект АПР', 1600.00, 0, 1, '', 0);
";
        $result = $this->modx->db->query($sql);
        if ($result)
            $res[] = 'Демо товары установлены 2 раза =))';
    }

    $sql = "UPDATE " . $this->modx->getFullTableName('site_content') . " set cacheable=0, content='[!MShopCatalog?tpl=`catalog_product_tpl` &parent=`0` &depth=`2` &limit=`10`&order=`content.menuindex DESC`!]' where id=" . $this->start_page;
    $result = $this->modx->db->query($sql);
    if ($result)
        $res[] = 'Кеш с входной страницы снят';

    if ($_POST['cart'] == 1) {
        $sql = "INSERT INTO ". $this->modx->getFullTableName('site_content') ." (`type`, `contentType`, `pagetitle`, `longtitle`, `description`, `alias`, `link_attributes`, `published`, `pub_date`, `unpub_date`, `parent`, `isfolder`, `introtext`, `content`, `richtext`, `template`) 
        VALUES ('document', 'text/html', 'Корзина', '', '', 'cart', '', 1, 0, 0, 0, 0, '', '<script type=\"text/javascript\" src=\"https://ajax.googleapis.com/ajax/libs/jquery/1.4.3/jquery.min.js\"></script>\r\n	[!MShopCart!]\r\n', 0, 0);";
        $result = $this->modx->db->query($sql);
        if ($result)
            $res[] = 'Страница для корзины создана';
    }


    $res[] = '<br><br>Еще больше документации на официальном сайте: <a href="http://mshop.rfweb.su/doc">http://mshop.rfweb.su/doc</a><br><br><br><br>';
}
?>
