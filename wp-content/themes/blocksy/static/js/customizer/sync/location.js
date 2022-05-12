import $ from 'jquery'
import { markImagesAsLoaded } from '../../frontend/lazy-load-helpers'
import ctEvents from 'ct-events'

const sendLocation = () => {
	wp.customize.selectiveRefresh.bind('partial-content-rendered', (e) => {
		if (!e.container) {
			return
		}

		markImagesAsLoaded(e.container[0])

		if ($) {
			$('.wc-tabs-wrapper, .woocommerce-tabs, #rating').trigger('init')
		}

		window.ctEvents.trigger('blocksy:frontend:init')
	})
}

wp.customize.bind('ready', () => sendLocation())
wp.customize.bind('preview-ready', () => sendLocation())
