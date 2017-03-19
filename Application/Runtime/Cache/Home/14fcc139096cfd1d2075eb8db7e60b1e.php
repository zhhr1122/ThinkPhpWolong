<?php if (!defined('THINK_PATH')) exit(); $config = D("Basic")->select(); ?>
<!doctype html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title><?php echo ($config["title"]); ?></title>
<meta name="keywords" content="<?php echo ($config["keywords"]); ?>" />
<meta name="description" content="<?php echo ($config["description"]); ?>" />
<link rel="stylesheet" href="/Public/css/bootstrap.min.css"
	type="text/css" />
<link rel="stylesheet" href="/Public/css/home/main.css" type="text/css" />
</head>
<body>
	<?php
 $navs = D("Menu")->getBarMenus(); ?>
<!doctype html>
<html lang="en">
<head>
<meta charset="UTF-8">
</head>
<body>
	<header id="header">
		<div class="navbar-inverse">
			<div class="container">
				<div class="navbar-header">
					<a href="/"> <img src="/Public/images/logo.png" alt="">
					</a>
				</div>
				<ul class="nav navbar-nav navbar-left">
					<li><a href="/" <?php if($result['catId'] == 0): ?>class="curr"<?php endif; ?>>首页</a></li>
					<?php if(is_array($navs)): foreach($navs as $key=>$vo): ?><li><a href="/index.php?c=cat&id=<?php echo ($vo["menu_id"]); ?>" <?php if($vo['menu_id'] == $result['catId']): ?>class="curr"<?php endif; ?>><?php echo ($vo["name"]); ?></a></li><?php endforeach; endif; ?>
				</ul>
			</div>
		</div>
	</header>
</body>
</html>
	<section>
		<div class="container">
			<div class="row">
				<div class="col-sm-9 col-md-9">
					<div class="news-list" style="overflow:hidden">
						<?php if(is_array($result['listNews'])): $i = 0; $__LIST__ = $result['listNews'];if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$vo): $mod = ($i % 2 );++$i;?><dl>
							<a target="_blank" href="/index.php?c=detail&id=<?php echo ($vo["news_id"]); ?>"><dt><?php echo ($vo["title"]); ?></dt></a>
							<dd class="news-img">
								<a target="_blank" href="/index.php?c=detail&id=<?php echo ($vo["news_id"]); ?>"><img
									width="200" height="120" src="<?php echo ($vo["thumb"]); ?>" alt="<?php echo ($vo["title"]); ?>"></a>
							</dd>
							<dd class="news-intro"><?php echo ($vo["description"]); ?></dd>
							<dd class="news-info">
								<?php echo ($vo["username"]); ?> <span><?php echo (date("Y-m-d
									H:i:s",$vo["create_time"])); ?></span> 阅读<i class="news_count node-<?php echo ($vo["news_id"]); ?>" news-id="<?php echo ($vo["news_id"]); ?>"><?php echo ($vo["count"]); ?></i>
							</dd>
						</dl><?php endforeach; endif; else: echo "" ;endif; ?>
					</div>
					<div style="clear:both"><?php echo ($result['pageRes']); ?></div>
					
					
				</div>
				<!doctype html>
<html lang="en">
<head>
<meta charset="UTF-8">
</head>
<body>
	<div class="col-sm-3 col-md-3">
		<div class="right-title">
			<h3>文章排行</h3>
			<span>TOP ARTICLES</span>
		</div>
		<div class="right-content">
			<ul>
				<?php if(is_array($result['rankNews'])): $k = 0; $__LIST__ = $result['rankNews'];if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$vo): $mod = ($k % 2 );++$k;?><li class="num<?php echo ($k); ?> curr"><a target="_blank"
					href="/index.php?c=detail&id=<?php echo ($vo["news_id"]); ?>"><?php echo ($vo["small_title"]); ?></a> <?php if($k == 1): ?><div class="intro"><?php echo ($vo["description"]); ?></div><?php endif; ?></li><?php endforeach; endif; else: echo "" ;endif; ?>
			</ul>
		</div>
		<?php if(is_array($result['advNews'])): $k = 0; $__LIST__ = $result['advNews'];if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$vo): $mod = ($k % 2 );++$k;?><div class="right-hot">
			<a target="_blank" href="/index.php?c=detail&id=<?php echo ($vo["news_id"]); ?>"><img
				src="<?php echo ($vo["thumb"]); ?>" alt="<?php echo ($vo["name"]); ?>"></a>
		</div><?php endforeach; endif; else: echo "" ;endif; ?>
	</div>
</body>
</html>
			</div>
		</div>
	</section>
	<!doctype html>
<html lang="en">
<div class="footer" style="    width: 100%;
    background: #262626;
    overflow: hidden;
    padding: 25px 0 25px 0;">
    <div style="width: 1002px;
    margin: auto;
    text-align: center;
    color: #bababa;
    line-height: 25px;">
        Copyright &copy; 2016 All Rights Reserved 版权所有 &copy; 深圳卧龙世纪科技有限公司
        粤ICP备16085155号-1　
        联系电话：0755-86100060　
        联系人：阿鑫                   </div>
    <div class="footer2" style="    width: 1002px;
    height: 63px;
    overflow: hidden;
    margin: auto;
    text-align: center;">
        <!--判断语句-->
        <img src="http://120.24.5.120/wp-content/uploads/2016/09/logo-1.png" />                       <!--输出语句-->
    </div>
    <div style=" text-align:center;"><script language=\"javascript\" type=\"text/javascript\" src=\"http://js.users.51.la/18970568.js\"></script></div>
</div>
</html>
</body>
</html>