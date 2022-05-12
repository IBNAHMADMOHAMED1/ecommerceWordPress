import ctEvents from 'ct-events'
import { getCurrentScreen } from 'blocksy-frontend'

import { computeShrink } from './sticky/shrink'
import { computeAutoHide } from './sticky/auto-hide'
import { computeFadeSlide } from './sticky/fade-slide'

import { getRowStickyHeight } from './sticky/shrink-utils'

export const setTransparencyFor = (deviceContainer, value = 'yes') => {
	Array.from(
		deviceContainer.querySelectorAll('[data-row][data-transparent-row]')
	).map((el) => {
		el.dataset.transparentRow = value
	})
}

var getParents = function (elem) {
	var parents = []

	for (; elem && elem !== document; elem = elem.parentNode) {
		parents.push(elem)
	}

	return parents
}

let cachedStartPosition = null
let cachedContainerInitialHeight = null
let cachedStickyContainerHeight = null

if (window.wp && wp.customize) {
	wp.customize.selectiveRefresh.bind(
		'partial-content-rendered',
		(placement) => {
			setTimeout(() => {
				cachedStartPosition = null
				cachedContainerInitialHeight = null
				cachedStickyContainerHeight = null
				prevScrollY = null

				compute()
			}, 500)
		}
	)
}

const getStartPositionFor = (stickyContainer) => {
	if (
		stickyContainer.dataset.sticky.indexOf('shrink') === -1 &&
		stickyContainer.dataset.sticky.indexOf('auto-hide') === -1
	) {
		return stickyContainer.parentNode.getBoundingClientRect().height + 200
	}

	const headerRect = stickyContainer.closest('header').getBoundingClientRect()

	let stickyOffset = headerRect.top + scrollY

	if (stickyOffset > 0) {
		let element = document.elementFromPoint(0, 3)

		if (element) {
			if (
				getParents(element)
					.map((el) => {
						let style = getComputedStyle(el)
						return style.position
					})
					.indexOf('fixed') > -1
			) {
				stickyOffset -= element.getBoundingClientRect().height
			}
		}
	}

	const row = stickyContainer.parentNode

	const bodyComp = getComputedStyle(document.body)
	let maybeDynamicOffset = parseFloat(
		bodyComp.getPropertyValue('--header-sticky-offset') || 0
	)

	if (
		row.parentNode.children.length === 1 ||
		row.parentNode.children[0].classList.contains('ct-sticky-container')
	) {
		return stickyOffset > 0
			? stickyOffset - maybeDynamicOffset
			: stickyOffset
	}

	let finalResult = Array.from(row.parentNode.children)
		.reduce((result, el, index) => {
			if (result.indexOf(0) > -1 || !el.dataset.row) {
				return [...result, 0]
			} else {
				return [
					...result,

					el.classList.contains('ct-sticky-container')
						? 0
						: el.getBoundingClientRect().height,
				]
			}
		}, [])
		.reduce((sum, height) => sum + height, stickyOffset)

	return finalResult > 0 ? finalResult - maybeDynamicOffset : finalResult
}

let prevScrollY = null

const compute = () => {
	if (prevScrollY === scrollY) {
		/*
		requestAnimationFrame(() => {
			compute()
		})
    */

		return
	}

	prevScrollY = scrollY

	const stickyContainer = document.querySelector(
		`[data-device="${getCurrentScreen()}"] [data-sticky]`
	)

	if (!stickyContainer) {
		return
	}

	let startPosition = cachedStartPosition

	if (startPosition === null) {
		startPosition = getStartPositionFor(stickyContainer)
		cachedStartPosition = startPosition
	}

	let stickyContainerHeight = cachedStickyContainerHeight

	if (!stickyContainerHeight) {
		stickyContainerHeight = parseInt(
			stickyContainer.getBoundingClientRect().height
		)
		cachedStickyContainerHeight = parseInt(stickyContainerHeight)

		document.body.style.setProperty(
			'--header-sticky-height-animated',
			`${[...stickyContainer.querySelectorAll('[data-row]')].reduce(
				(res, row) => res + getRowStickyHeight(row),
				0
			)}px`
		)
	}

	const stickyComponents = stickyContainer.dataset.sticky
		.split(':')
		.filter((c) => c !== 'yes' && c !== 'no' && c !== 'fixed')

	let isSticky =
		(startPosition > 0 && Math.abs(window.scrollY - startPosition) < 5) ||
		window.scrollY > startPosition

	if (stickyComponents.indexOf('shrink') > -1) {
		isSticky =
			startPosition > 0
				? window.scrollY >= startPosition
				: window.scrollY > 0
	}

	setTimeout(() => {
		if (isSticky && document.body.dataset.header.indexOf('shrink') === -1) {
			document.body.dataset.header = `${document.body.dataset.header}:shrink`
		}

		if (!isSticky && document.body.dataset.header.indexOf('shrink') > -1) {
			document.body.dataset.header = document.body.dataset.header.replace(
				':shrink',
				''
			)
		}
	}, 300)

	let containerInitialHeight = cachedContainerInitialHeight

	if (!containerInitialHeight) {
		cachedContainerInitialHeight = Array.from(
			stickyContainer.querySelectorAll('[data-row]')
		).reduce((sum, el) => sum + el.getBoundingClientRect().height, 0)

		containerInitialHeight = cachedContainerInitialHeight

		stickyContainer.parentNode.style.height = `${containerInitialHeight}px`
	}

	if (stickyComponents.indexOf('shrink') > -1) {
		computeShrink({
			stickyContainer,
			stickyContainerHeight,

			containerInitialHeight,
			isSticky,
			startPosition,
			stickyComponents,
		})
	}

	if (stickyComponents.indexOf('auto-hide') > -1) {
		computeAutoHide({
			stickyContainer,
			isSticky,
			startPosition,
			stickyComponents,
		})
	}

	if (
		stickyComponents.indexOf('slide') > -1 ||
		stickyComponents.indexOf('fade') > -1
	) {
		computeFadeSlide({
			stickyContainer,
			isSticky,
			startPosition,
			stickyComponents,
		})
	}
}

export const mountStickyHeader = () => {
	if (!document.querySelector('header [data-sticky]')) {
		return
	}

	window.addEventListener(
		'resize',
		(event) => {
			compute(event)
			ctEvents.trigger('ct:header:update')
		},
		false
	)

	window.addEventListener('orientationchange', (event) => {
		compute(event)
		ctEvents.trigger('ct:header:update')
	})

	window.addEventListener('scroll', compute, false)
	window.addEventListener('load', compute, false)

	compute()
}
