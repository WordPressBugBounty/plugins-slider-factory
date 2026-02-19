var SF_Layout11_Slider = (function () {

	var $container, $contentwrapper, $items, itemsCount, $slidewrapper, $slidescontainer, $slides, $navprev, $navnext, current, isAnimating, support, transEndEventNames, transEndEventName;

	var init = function () {
		$container = jQuery('#ps-container');
		$contentwrapper = $container.children('div.ps-contentwrapper');
		// the items (description elements for the slides/products)
		$items = $contentwrapper.children('div.ps-content');
		itemsCount = $items.length;
		$slidewrapper = $container.children('div.ps-slidewrapper');
		// the slides (product images)
		$slidescontainer = $slidewrapper.find('div.ps-slides');

		$slides = $slidescontainer.children('div');
		// navigation arrows
		$navprev = $slidewrapper.find('nav > a.ps-prev');
		$navnext = $slidewrapper.find('nav > a.ps-next');

		// current index for items and slides
		current = 0;
		// checks if the transition is in progress
		isAnimating = false;
		// support for CSS transitions
		support = Modernizr.csstransitions;
		// transition end event
		// https://github.com/twitter/bootstrap/issues/2870
		transEndEventNames = {
			'WebkitTransition': 'webkitTransitionEnd',
			'MozTransition': 'transitionend',
			'OTransition': 'oTransitionEnd',
			'msTransition': 'MSTransitionEnd',
			'transition': 'transitionend'
		};
		// its name
		transEndEventName = transEndEventNames[Modernizr.prefixed('transition')];

		// show first item
		var $currentItem = $items.eq(current),
			$currentSlide = $slides.eq(current);

		$currentItem.addClass('ps-active');
		$currentSlide.addClass('ps-active');

		// update nav images
		updateNavImages();

		// initialize some events
		initEvents();

	},
		updateNavImages = function () {

			// updates the background image for the navigation arrows
			var configPrev = (current > 0) ? $slides.eq(current - 1).css('background-image') : $slides.eq(itemsCount - 1).css('background-image'),
				configNext = (current < itemsCount - 1) ? $slides.eq(current + 1).css('background-image') : $slides.eq(0).css('background-image');

			$navprev.css('background-image', configPrev);
			$navnext.css('background-image', configNext);

		},
		initEvents = function () {

			$navprev.on(
				'click',
				function (event) {

					if (!isAnimating) {

						slide('prev');

					}
					return false;

				}
			);

			$navnext.on(
				'click',
				function (event) {

					if (!isAnimating) {

						slide('next');

					}
					return false;

				}
			);

			// transition end event
			$items.on(transEndEventName, removeTransition);
			$slides.on(transEndEventName, removeTransition);

		},
		removeTransition = function () {

			isAnimating = false;
			jQuery(this).removeClass('ps-move');

		},
		slide = function (dir) {

			isAnimating = true;

			var $currentItem = $items.eq(current),
				$currentSlide = $slides.eq(current);

			// update current value
			if (dir === 'next') {

				(current < itemsCount - 1) ? ++current : current = 0;

			} else if (dir === 'prev') {

				(current > 0) ? --current : current = itemsCount - 1;

			}
			// Logic for CSS Class based transition (Flexbox friendly)

			// Remove active class from CURRENT
			$currentItem.removeClass('ps-active');
			$currentSlide.removeClass('ps-active');

			// Add active class to NEW
			var $newItem = $items.eq(current);
			var $newSlide = $slides.eq(current);

			$newItem.addClass('ps-active');
			$newSlide.addClass('ps-active');

			isAnimating = false; // Transition handled by CSS
			updateNavImages();


			// update nav images
			updateNavImages();

		};

	return { init: init };

})();
