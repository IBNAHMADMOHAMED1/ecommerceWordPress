import ctEvents from 'ct-events'
import lazyload from 'vanilla-lazyload'

import { registerDynamicChunk } from 'blocksy-frontend'

let lz = null

const maybeInit = () => {
	if (lz) {
		lz.update()
		return
	}

	const cb = (img) => {
		let action = () => {
			let container = img.closest('[class*="ct-image-container"]')
			if (!container) return

			container.classList.remove('ct-lazy')
			container.classList.add('ct-lazy-loading-start')

			requestAnimationFrame(() => {
				container.classList.remove('ct-lazy-loading-start')
				container.classList.add('ct-lazy-loading')

				whenTransitionEnds(container.firstElementChild, () => {
					container.classList.remove('ct-lazy-loading')
					container.classList.add('ct-lazy-loaded')
				})
			})
		}

		if (navigator.userAgent.toLowerCase().indexOf('firefox') > -1) {
			setTimeout(action, 500)
		} else {
			action()
		}
	}

	const options = {
		data_src: 'ct-lazy',
		data_srcset: 'ct-lazy-set',

		threshold: 100,

		elements_selector: 'img[data-ct-lazy]',

		callback_load: (img) => {
			cb(img)

			if (img.srcset === 'false') {
				img.removeAttribute('srcset')
			}
		},
		callback_loaded: cb,
	}

	lz = new lazyload(options)
}

function whenTransitionEnds(el, cb) {
	const end = () => {
		el.removeEventListener('transitionend', onEnd)
		cb()
	}

	const onEnd = (e) => {
		if (e.target === el) {
			end()
		}
	}

	el.addEventListener('transitionend', onEnd)
}

let mounted = false

registerDynamicChunk('blocksy_lazy_load', {
	mount: (el, { event }) => {
		if (mounted) {
			return
		}

		mounted = true

		if (window.jQuery) {
			window.jQuery(window).on('elementor/frontend/init', () => {
				elementorFrontend.hooks.addAction(
					'frontend/element_ready/global',
					() => ctEvents.trigger('ct:images:lazyload:update')
				)
			})

			window.jQuery(document.body).on('ubermenuopen', function () {
				ctEvents.trigger('ct:images:lazyload:update')
			})

			window.jQuery(window).on('wcpf_update_products', function () {
				ctEvents.trigger('ct:images:lazyload:update')
			})

			window.jQuery(document).on('ready', function () {
				ctEvents.trigger('ct:images:lazyload:update')
			})

			window
				.jQuery(document)
				.on('wpf_ajax_success', () =>
					ctEvents.trigger('ct:images:lazyload:update')
				)
		}

		if (document.querySelector('img[data-ct-lazy]')) {
			maybeInit()
		}

		ctEvents.on('ct:images:lazyload:update', () => {
			if (window.jQuery) {
				window.jQuery('body').trigger('jetpack-lazy-images-load')
			}

			if (window.jetpackLazyImagesModule) {
				window.jetpackLazyImagesModule()
			}

			let jetpackEvent = new Event('jetpack-lazy-images-load')
			document.body.dispatchEvent(jetpackEvent)

			maybeInit()
		})
	},
})
