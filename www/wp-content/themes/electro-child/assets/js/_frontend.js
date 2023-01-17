(function($){
	"use strict";

	jQuery(document).ready(function($){

		var options = {
			w: $(window).width(),
			theta_carousel_static : {
				enabled: false,
				selectedIndex: 1,
				distance: 150,
				numberOfElementsToDisplayRight: 1,
				numberOfElementsToDisplayLeft: 1,
				designedForWidth: 1294,
				designedForHeight: 712,
				fallback: 'never',
				mode3D: 'scale',
				sensitivity: 0.5,
				path: {
					type: 'cubic_bezier',
					settings: {
						width: 2300,
						depth: 340,
						xyPathBezierPoints: {
							p1: {
								x: -100,
								y: 0
							},
							p2: {
								x: 0,
								y: 0
							},
							p3: {
								x: 0,
								y: 0
							},
							p4: {
								x: 100,
								y: 0
							}
						},
						xyArcLengthBezierPoints: {
							p1: {
								x: 0,
								y: 0
							},
							p2: {
								x: 100,
								y: 10
							},
							p3: {
								x: 0,
								y: 90
							},
							p4: {
								x: 100,
								y: 100
							}
						},
						xzPathBezierPoints: {
							p1: {
								x: -100,
								y: 50
							},
							p2: {
								x: 0,
								y: 0
							},
							p3: {
								x: 0,
								y: 0
							},
							p4: {
								x: 100,
								y: 50
							}
						}
					}
				},
				sizeAdjustment: true,
				sizeAdjustmentBezierPoints: {
					p1: {
						x: 0,
						y: 100
					},
					p2: {
						x: 30,
						y: 70
					},
					p3: {
						x: 70,
						y: 30
					},
					p4: {
						x: 100,
						y: 0
					}
				}
			},
			theta_carousel_dynamic : {
				enabled: true,
				selectedIndex: 3,
				distance: 15,
				numberOfElementsToDisplayRight: 3,
				numberOfElementsToDisplayLeft: 3,
				designedForWidth: 1294,
				designedForHeight: 724,
				distanceInFallbackMode: 300,
				scaleX: 2,
				path: {
					settings: {
						shiftZ: -712,
						a: 430,
						b: 700,
						endless: true
					},
					type: 'ellipse'
				},
				perspective: 956,
				sensitivity: 0.2,
				mousewheelEnabled: false,
				sizeAdjustment: true,
				sizeAdjustmentNumberOfConfigurableElements: 4,
				sizeAdjustmentBezierPoints: {
					p1: {
						x: 0,
						y: 100
					},
					p2: {
						x: 1,
						y: 61
					},
					p3: {
						x: 5,
						y: 72
					},
					p4: {
						x: 100,
						y: 72
					}
				}
			},
			owl_carusel_triple: {
				center: true,
				items: 3,
				loop: true,
				margin: 0,
				nav: true,
				navText: [],
				dots: false,
				autoplay: false,
				autoplayTimeout:4000,
				autoplayHoverPause:true,
				responsive:{
					0:{items:1, margin:0},
					767:{items:2, margin:15, center:false},
					1024:{items:3, margin:26}
				}
			},
			owl_carusel_single: {
				center: true,
				items: 1,
				loop: true,
				margin: 0,
				nav: true,
				dots: false,
				autoplay: false,
				autoplayTimeout:4000,
				autoplayHoverPause:true,
				responsive:{}
			},
			owl_carusel_withthumbs: {
				center: true,
				items: 1,
				loop: true,
				margin: 0,
				nav: false,
				dots: false,
				autoplay: false,
				autoplayTimeout:4000,
				autoplayHoverPause:true,
				responsive:{}
			},
			slick_options:{
				slidesToShow: 1,
				slidesToScroll: 1,
				dots: false,
				centerMode: true,
				centerPadding: '0px',
				focusOnSelect: true,
				infinite: true,
				appendArrows: $('#slider-navs'),
				prevArrow: '<a href="#" class="slick-prev arrow prev" aria-label="Previous">&lsaquo;</a>',
				nextArrow: '<a href="#" class="slick-next arrow next" aria-label="Next">&rsaquo;</a>',
				/*responsive: [
					{breakpoint: 768, settings: {centerPadding: '0px', slidesToShow: 1}},
					{breakpoint: 480, settings: {centerPadding: '0px', slidesToShow: 1}},
				]*/
			},
		};

		var $window = $(window);

		var _createClass = function(){
			function defineProperties(target, props){
				for(var i = 0; i < props.length; i++){
					var descriptor = props[i];
					descriptor.enumerable = descriptor.enumerable || false;
					descriptor.configurable = true;
					if("value" in descriptor) descriptor.writable = true;
					Object.defineProperty(target, descriptor.key, descriptor);
				}
			}

			return function(Constructor, protoProps, staticProps){
				if(protoProps) defineProperties(Constructor.prototype, protoProps);
				if(staticProps) defineProperties(Constructor, staticProps);
				return Constructor;
			};
		}();

		var _classCallCheck = function(instance, Constructor){
			if(!(instance instanceof Constructor)){
				throw new TypeError("Cannot call a class as a function");
			}
		};

		var _setCookies = function(cname, cvalue, exdays) {
			var d = new Date();
			d.setTime(d.getTime() + exdays * 24 * 60 * 60 * 1000);
			var expires = "expires=" + d.toUTCString();
			document.cookie = cname + "=" + cvalue + ";" + expires + ";path=/";
		};

		var Header = function(){

			//Initialize Header @function
			function Header() {
				_classCallCheck(this, Header);

				this.MENU_STATUS = 'closed';
				this.mainNav = $("#main-nav");
				this.mainNavLinks = this.mainNav.find(".nav-link");
				this.$mainOverlay = $('.main-overlay');
				this.$hamburger = $('#menu-btn');

				this.USER_MENU_STATUS = 'closed';
				this.userNav = $("#user-nav");
				this.$userOverlay = $('.user-overlay');
				this.$usertrigger = $('#icon-user');
				this.$userCloseTrigger = $('.user-menu-close');
				this.$userMenu = $('#user-menu');

				//this.mainNav.find("li:first").find(".nav-link").addClass('active');

				this.events();
			}

			//Open/Close the Menu@function
			_createClass(Header, [
				{key: 'toggleMenu', value: function toggleMenu(){
					// this.$searchModal.removeClass('open');
					if (this.MENU_STATUS === 'closed') {
						// this.overlay.open();

						if (window.innerWidth <= 1024) {
							$("body").addClass('nav-open');
							this.$hamburger.addClass('open');
							this.$mainOverlay.fadeIn();
						}
						this.MENU_STATUS = 'open';
					} else {
						if (window.innerWidth <= 1024) {
							$("body").removeClass('nav-open');
							this.$hamburger.removeClass('open');
							this.$mainOverlay.fadeOut();
						}
						this.MENU_STATUS = 'closed';
					}
				}},
				{key: 'toggleUserMenu', value: function toggleUserMenu(event){
					// this.$searchModal.removeClass('open');
					if(this.$userMenu.length){
						if(this.USER_MENU_STATUS === 'closed'){
							// this.overlay.open();

							if(window.innerWidth <= 1024){
								$("body").addClass('user-nav-open');
								this.$usertrigger.addClass('open');
								this.$userOverlay.fadeIn();
								this.$hamburger.hide();
							}
							this.USER_MENU_STATUS = 'open';
						}else{
							if(window.innerWidth <= 1024){
								$("body").removeClass('user-nav-open');
								this.$usertrigger.removeClass('open');
								this.$userOverlay.fadeOut();
								this.$hamburger.show();
							}
							this.USER_MENU_STATUS = 'closed';
						}
						event.preventDefault();
					}
				}},
				{key: 'onScroll', value: function onScroll(){
					/*var scrollPos = $(document).scrollTop();
					var obj = this;
					obj.mainNavLinks.each(function(){
						var currLink = $(this);
						var refElement = $(currLink.attr("href"));
						console.log(refElement);
						if (refElement.position().top - 240 <= scrollPos && refElement.position().top + refElement.height() > scrollPos) {
							obj.mainNavLinks.removeClass("active");
							currLink.addClass("active");
						} else if ($(window).scrollTop() + $(window).height() == $(document).height()) {
							obj.mainNavLinks.removeClass("active");
							obj.mainNavLinks.last().addClass("active");
						} else {
							currLink.removeClass("active");
						}
					});*/
				}},
				{key: 'events', value: function events(){
					var _this = this;

					var obj = this;
					$(document).on("scroll", function () {
						_this.onScroll();
					});

					this.$mainOverlay.on('click', this.toggleMenu.bind(this));
					this.$hamburger.on('click', this.toggleMenu.bind(this));

					this.$userCloseTrigger.on('click', this.toggleUserMenu.bind(this));
					this.$userOverlay.on('click', this.toggleUserMenu.bind(this));
					this.$usertrigger.on('click', this.toggleUserMenu.bind(this));

					this.mainNavLinks.on("click", function (event) {
						var $this = $(this),
							href = $this.attr("href");
						if(href.indexOf('#')){
							var a = href.split('#');
							href = '#' + a[a.length - 1];
							scrollToHash(href);
							obj.toggleMenu();
							event.preventDefault();
						}
						return false;
					});

					var $body = $("body");

					var navBarHeight = obj.mainNav.height(),
						scrollDown = false,
						lastScroll = -1;

					function scrollToHash(hash){
						if($(hash+'-section').length == 0){
							return;
						}
						var topY = $(hash+'-section').offset().top,
							mainMenuHeight = parseInt($('#site-header').height()),
							sp = Math.abs(window.scrollY - topY) / 2;

						if($body.hasClass('desktop-header-fixed') || $body.hasClass('mobile-header-fixed')){
							topY -= mainMenuHeight;
						}

						$('html, body').stop().animate({
							scrollTop: topY
						}, sp);

						return false;
					}

					$(window).on('load', function () {
						if(window.location.hash) {
							$('html, body').stop();
							scrollToHash(window.location.hash);
						}
					});

					function update() {
						var top = window.scrollY || window.pageYOffset;
						// console.log(top);
						if (top > lastScroll) {
							//Scroll down
							if (lastScroll >= 0) {
								if (top > window.innerHeight - navBarHeight) {
									fnScrollDown();
								}
								scrollDown = true;
							}
						} else if (top < window.innerHeight - navBarHeight) {
							//Scroll up
							// lastScroll
							fnScrollUp(top);
							scrollDown = false;
						} else {
							if (scrollDown) {
								fnScrollDown();
							} else {
								fnScrollUp(top);
							}
						}

						lastScroll = top;
					}

					function fnScrollDown() {
						// Scroll Down
						if (!$body.hasClass("nav-open")) {
							$body.removeClass('nav-down').addClass('nav-up');
						}
					}

					function fnScrollUp(st) {
						// Scroll Up
						if (st + $(window).height() < $(document).height()) {
							$body.removeClass('nav-up').addClass('nav-down');
						}
					}

					var collapseToggleHeader = function collapseToggleHeader() {
						update();
					};

					var scroll = function scroll() {
						collapseToggleHeader();
					};
					var raf = window.requestAnimationFrame || window.webkitRequestAnimationFrame || window.mozRequestAnimationFrame || window.msRequestAnimationFrame || window.oRequestAnimationFrame;
					var $window = $(window);
					var lastScrollTop = $window.scrollTop();

					if (raf) {
						loop();
					}

					function loop() {
						var scrollTop = $window.scrollTop();
						if (lastScrollTop === scrollTop) {
							raf(loop);
							return;
						} else {
							lastScrollTop = scrollTop;

							// fire scroll function if scrolls vertically
							scroll();
							raf(loop);
						}
					}
				}},
				{key: 'update', value: function update(percentage) {}}
			]);

			return Header;
		}();

		var cookies_box = function(){
			var str1 = document.cookie;
			var str2 = 'acceptCookies=10';
			if(str1.indexOf(str2) != -1){
				$('.cookies-box').hide();
			}

			$('.cookies-box').find('.got-it-btn').on('click', function (event) {
				event.preventDefault();
				_setCookies('acceptCookies', 10, 365);
				$(this).closest('.cookies-box').fadeOut();
				/* Act on the event */
			});
		};

		var set_products_equal_height = function(){
			if($('.js-equal-height-products').length){
				var devices = $('.js-equal-height-products').data('devices');
				var dt = devices.split(',');
				var h = [];
				//console.log(dt);
				if($.inArray(globals.device, dt) != -1){
					$('.js-equal-height-products')
						.find('figure').addClass('pr of-h').removeAttr('style')
						.end()
						.find('.inner').addClass('pr').removeAttr('style');
					$('.js-equal-height-products figure').each(function(){
						h.push($(this).height());
					});
					var min_height = Math.round(Math.min.apply(null, h));
					$('.js-equal-height-products figure').height(min_height).removeClass('pr');

					h = [];
					$('.js-equal-height-products .inner').each(function(){
						h.push($(this).height());
					});
					var max_height = Math.round(Math.max.apply(null, h));
					$('.js-equal-height-products .inner').height(max_height).removeClass('pr');
				}
			}
		};

		var set_items_equal_height = function(){
			if($('.js-equal-height-items').length){
				var devices = $('.js-equal-height-items').data('equal-height');
				var direction = $('.js-equal-height-items').data('direction');
				var dt = devices.split(',');
				var h = 0;
				if(direction == 'by-min'){
					h = 99999999;
				}
				//console.log(dt);
				if($.inArray(globals.device, dt) != -1){
					if($('.js-equal-height-items .equal-height').length > 1){
						$('.js-equal-height-items .equal-height').addClass('pr').removeAttr('style');
						$('.js-equal-height-items .equal-height').each(function(){
							console.log($(this).height());
							switch(direction){
								case "by-max":
									if($(this).height() > h){
										h = $(this).height();
									}
									break;
								case "by-min":
									if($(this).height() < h){
										h = $(this).height();
									}
									break;
							}
						});
						$('.js-equal-height-items .equal-height').height(Math.round(h)).removeClass('pr');
					}
				}
			}
		};

		var set_cols_equal_height = function(){
			if($('.js-equal-height-columns').length){
				var devices = $('.js-equal-height-columns').data('equal-height');
				var dt = devices.split(',');
				//console.log(dt);
				if($.inArray(globals.device, dt) != -1){
					$('.js-equal-height-columns').each(function(){
						var $parent = $(this);
						var $sc = $parent.find('.js_src_col');
						var $dc = $parent.find('.js_dst_col');
						$dc.removeClass('ipa').removeAttr('style');
						//console.log($sc.height());
						if(~~$dc.height() < ~~$sc.height()){
							$dc.height($sc.height()).addClass('ipa');
						}
					});
				}
			}
		};

		var set_page_title_height = function(){
			var h = $('.header .page-title h1').height();
			switch(globals.device){
				case 'mobile':
					//h = 65;
					break;
				case 'tablet':
					//h = 123;
					break;
				case 'desktop':
					//h = 182;
					break;
			}
			$('.header .page-title').height(h).addClass('vm animated fadeInLeft');
		};

		var apply_owl_carusel = function(){
			$('.items_slider.owl-carousel').each(function(i, el){

				var container = $(el);
				var nav_display = ~~container.data('nav');
				var items_count = ~~container.data('items');
				var color = container.data('color');
				var slider_type = container.data('type');
				var autoplay = container.data('autoplay');
				var loop = container.data('loop');
				var center = container.data('center');
				var childs_slider = container.data('childs');
				var parent_slider = container.data('parent');
				var slider_options = {};
				var duration = 500;

				if(undefined == loop){
					loop = true;
				}

				//console.log(nav_display);

				if(undefined == nav_display){
					nav_display = true;
				}else{
					nav_display = (nav_display == 1) ? true : false;
				}

				if(loop){
					nav_display = loop;
				}
				//console.log(slider_options.nav);

				if(undefined == center){
					center = true;
				}

				if(childs_slider){
					childs_slider = $(childs_slider);
				}else{
					childs_slider = false;
				}

				if(parent_slider){
					parent_slider = $(parent_slider);
				}else{
					parent_slider = false;
				}


				container.on('initialized.owl.carousel', function(event){
					setTimeout(function(){
						container.find('.owl-nav').addClass(color);
						if(nav_display){
							container.find('.owl-nav').removeClass('disabled');
						}
					}, 100);
				}).on('translate.owl.carousel', function(event){
					//console.log(event);
					//$(this).find('.owl-item').css({'opacity':1});
					if(nav_display){
						$('.owl-nav').removeClass('disabled');
					}
				}).on('translated.owl.carousel', function(event){
					//console.log(event);
					//$(this).find('.owl-item').not('.active').css({'opacity':0});
					//$(this).find('.owl-item').css({'opacity':0}).end().find('.active').css({'opacity':1});
					if(nav_display){
						$('.owl-nav').removeClass('disabled');
					}
				}).on('changed.owl.carousel', function(e){
					//On change of main item to trigger thumbnail item
					if(childs_slider != false){
						//childs_slider.trigger('to.owl.carousel', [e.item.index, duration, true]);
						//childs_slider.data('owl.carousel').to(e.item.index, duration, true);
					}
					if(parent_slider != false){
						parent_slider.trigger('to.owl.carousel', [e.item.index, duration, true]);
						//parent_slider.data('owl.carousel').to(e.item.index, duration, true);
					}
				}).on('click', '.owl-item', function(){
					// On click of thumbnail items to trigger same main item
					if(parent_slider != false){
						parent_slider.trigger('to.owl.carousel', [$(this).index(), duration, true]);
						//parent_slider.data('owl.carousel').to($(this).index(), duration, true);
					}
				});

				switch(slider_type){
					case "triple":
						slider_options = options.owl_carusel_triple;
						slider_options.items = items_count;
						slider_options.responsive[1024].items = items_count;
						break;
					case "single":
						slider_options = options.owl_carusel_single;
						break;
					case "withthumbs":
						slider_options = options.owl_carusel_withthumbs;
						break;
				}

				slider_options.nav = nav_display;
				slider_options.center = center;
				slider_options.loop = loop;
				slider_options.autoplay = autoplay;

				container.delay(duration).animate({opacity: 1}, duration).owlCarousel(slider_options);
				//container = null;
			});
		};

		if($('.btn-video-play').length){
			$('.btn-video-play').each(function(i, el){
				var video_id = $(el).parent().find('video').attr('id');
				var video = document.getElementById(video_id);
				var $cover = $(el).parent().find('.cover');

				$(el).on('click', function(){
					$(this).fadeOut(500)
					$cover.fadeOut(2000);
					video.play();
				});

				video.onended = function(e) {
					$(el).fadeIn(500)
					$cover.fadeIn(2000);
				};
			});
		}

		if($('.items_slider.slick').length){
			$('.items_slider.slick').each(function(i, el){
				var container = $(el);
				var items_count = ~~container.data('items');
				var childs_slider = container.data('childs');
				var parent_slider = container.data('parent');

				options.slick_options.slidesToShow = items_count;

				if(childs_slider){
					options.slick_options.asNavFor = $(childs_slider);
				}

				if(parent_slider){
					options.slick_options.asNavFor = $(parent_slider);
				}


				container
					.delay(500).animate({opacity: 1}, 500)
					.slick(options.slick_options).on('beforeChange', function(event, slick, currentSlide, nextSlide){
					//$('.slick-current .item').removeClass('zoom');
				}).on('afterChange', function(event, slick, currentSlide, nextSlide){
					//$(slick.$slides.get(currentSlide)).css({'transform':'scale(1.05)'});
					//console.log(currentSlide, $(slick.$slides.get(currentSlide)))
					//$('.slick-current .item').addClass('zoom');
				});
			});
		}

		if($('.items_slider.owl-carousel').length){
			apply_owl_carusel();
		}

		if($('.items_slider.theta-carousel').length){
			//console.log($.theta.carousel.version);
			var container = $('.items_slider.theta-carousel');
			var items_count = ~~container.data('items');
			var opts = options.theta_carousel_static;

			//console.log(items_count);
			if(items_count > 3){
				opts = options.theta_carousel_dynamic;
				var navs = $('#slider-navs');
				var color = navs.data('color');
				var html = '<div class="theta-nav">' +
					'<a role="button" class="theta-prev arrow bg-'+color+' prev"></a>' +
					'<a role="button" class="theta-next arrow bg-'+color+' next"></a>' +
					'</div>';
				navs.append(html).removeClass('hidden');
			}
			//console.log(opts);

			container
				.delay(500).animate({opacity: 1}, 500)
				.theta_carousel(opts);

			if(navs != undefined){
				navs.find('a').on('click', function(){
					if($(this).hasClass('next')){
						container.theta_carousel('moveForward');
					}
					if($(this).hasClass('prev')){
						container.theta_carousel('moveBack');
					}
				});
			}
		}

		if($('.items_slider').length){
			$('.items_slider').find('[data-toggle="collapse"]').on('click', function(){
				//var $parent = $($(this).data('parent'));
				var $target = $($(this).data('target'));
				//console.log($target);
				if($target.hasClass('in')){
					$(this).addClass('collapsed');
				}else{
					$(this).removeClass('collapsed');
				}
			});
		}

		if($('.button.loadmore').length){
			$('.button.loadmore').on('click', function(e){
				e.preventDefault();

				var $this = $(this);
				var $parent = $(this).parents('.loadmore-row');
				var offset = ~~$parent.data('offset');
				var count = ~~$parent.data('count');
				var cpt = $parent.data('cpt');
				var dataObj = {'action': 'load_more_posts', 'offset': offset, 'cpt': cpt, 'count': count, 'nonce': globals.nonce}

				$this.addClass('loader');

				$.ajax({
					url: globals.ajax_url,
					data: dataObj,
					dataType: 'json',
					type: "POST",
				}).done(function(data){
					console.log(data);
					if(data.error == 0){
						$parent.data('offset', data.offset);
						$parent.before(data.html);
						if(cpt == 'rental'){
							setTimeout(function(){
								apply_owl_carusel();
							}, 500);
						}
						$this.removeClass('loader');
						if(data.is_load_more < 1){
							$parent.hide();
						}
					}
				}).fail(function(errorThrown){
					$this.removeClass('loader');
					console.log(errorThrown);
				});

			});
		}

		$('*[data-type="parallax"]').each(function(){
			var $_this = $(this);
			var t = $_this.offset().top,
				wh = $window.height(),
				h = $_this.height(),
				$content_item = $_this.find('.content-item'),
				speed = $_this.data('speed');

			$(window).scroll(function() {
				var wst = $window.scrollTop();

				/*if(wst+wh > t){
					var yPos = -((wst-wh-h) / speed);
					var coords = '50% -' + yPos + 'px';
					$_this.css({backgroundPosition: coords});
				}*/

				if(wst+wh > t+(h/2)){
					if(!$content_item.hasClass('show')){
						$content_item.addClass('show');
					}
				}else{
					if($content_item.hasClass('show')){
						//$content_item.removeClass('show');
					}
				}
			});
		});

		$('[data-action="js_action"]').on('click', function(e){
			var $this = $(this);
			var type = $(this).data('type');
			switch(type){
				default:
					break;
			}
		});

		if($('.qty-alt').length){
			$('.qty-alt').on('change', function(e){
				e.preventDefault();
				e.stopPropagation();
			});
		}

		if($('.product-categories').length){
			if(globals.device == 'mobile' || options.w < 768){
				$('.product-categories').prepend('<li id="cat_item_show_all" class="cat-item cat-item-0"><a href="/shop/">' + globals.lang.show_all + '</a></li>');
			}
		}

		if($('ul#pa_sizes').length){
			$('ul#pa_sizes').addClass('attribute_pa_sizes');
			var item_selected = $('ul#pa_sizes').data('selected');
			$('ul#pa_sizes').find('li').each(function(){
				var $el = $(this).find('input[type="radio"]');
				if($el.val() == ''){
					$(this).remove();
				}else if($el.val() == item_selected){
					$el.attr('checked', true);
					$el.parent('label').addClass('checked');
				}
			});
			$('ul#pa_sizes').on('click', 'input[type="radio"]', function(){
				$('ul#pa_sizes').find('label').removeClass('checked');
				$(this).parent('label').addClass('checked');
				var value = $(this).val();
				//$('select#pa_sizes').find('option[value="'+value+'"]').attr('selected', true).end().trigger('change');
				$('select#pa_sizes').val(value).trigger('change');
			});
		}

		if($('.woocommerce').find('#catalog_filter').length){
			$('.woocommerce').on('click', '#catalog_filter', function(e){
				e.preventDefault();
				$('.filter-overlay').fadeIn();
				$('.catalog-filter').addClass('show');
			});
			$('.woocommerce').on('click', '.btn-back-filter', function(e){
				e.preventDefault();
				$('.catalog-filter').removeClass('show');
				$('.filter-overlay').fadeOut();
			});
		}

		$(document).on('click', '.shop-view-switcher .nav-link', function(){
			$.cookie('product-view-type', $(this).data('archiveClass'), {path: '/', expires: new Date(Date.now() + (365 * 86400 * 1000))});
			$('[data-toggle="shop-products"]').attr('data-view', $(this).data('archiveClass'));
		});

		//new Header();
		//cookies_box();
		//set_page_title_height();
		/*setTimeout(function(){
			set_products_equal_height();
			set_items_equal_height();
			set_cols_equal_height();
		}, 200);*/
		//set_items_equal_height();
		//set_cols_equal_height();

		$(window).on('resize orientationchange deviceorientation', function(){
			options.w = $(window).width();
			//set_products_equal_height();
			//set_page_title_height();
			//set_items_equal_height();
			//set_cols_equal_height();
		});

	}).on('wpcf7mailsent', function(event){
		console.log('wpcf7mailsent');
		//console.log(event.detail.contactFormId, globals.form_id);
		if(~~event.detail.contactFormId == ~~globals.form_id){
			//$('#js_ajax_message').find('.info').text(event.detail.apiResponse.message).end().delay(1000).fadeOut(200);
		}
	}).on('wpcf7invalid', function(event){
		console.log('wpcf7invalid');
		$('html, body').stop().animate({
			scrollTop: $(".wpcf7-not-valid-tip").first().offset().top-100
		}, 800);
	}).on('wpcf7mailfailed', function(event){
		console.log('wpcf7mailfailed');
		if(~~event.detail.contactFormId == ~~globals.form_id){
			//$('#js_ajax_message').find('.info').text(event.detail.apiResponse.message).end().delay(1000).fadeOut(200);
		}
	}).on('wpcf7submit', function(event){
		console.log('wpcf7submit');
		if(~~event.detail.contactFormId == ~~globals.form_id){
			//$('#js_ajax_message').find('.info').text(event.detail.apiResponse.message).end().delay(1000).fadeOut(200);
		}
	}).on('wpcf7spam', function(event){
		console.log('wpcf7spam');
		if(~~event.detail.contactFormId == ~~globals.form_id){
			//$('#js_ajax_message').find('.info').text(event.detail.apiResponse.message).end().delay(1000).fadeOut(200);
		}
	});

	if(window.WOW){
		var wow = new WOW({
			boxClass: 'wow',
			animateClass: 'animated',
			offset: 200,
			mobile: false,
			live: true
		});
		wow.init();
	}

})(jQuery);
