import { setTransparencyFor } from '../sticky'

let prevScrollY = window.scrollY

export const computeAutoHide = ({
	startPosition,
	stickyContainer,
	isSticky,
	stickyComponents,
}) => {
	if (window.scrollY < startPosition) {
		prevScrollY = window.scrollY
	}

	if (isSticky && window.scrollY - prevScrollY === 0) {
		document.body.style.setProperty(
			'--header-sticky-height-animated',
			`0px`
		)
	}

	if (isSticky && window.scrollY - prevScrollY < -5) {
		if (stickyContainer.dataset.sticky.indexOf('yes') === -1) {
			stickyContainer.dataset.sticky = [
				'yes-start',
				...stickyComponents,
			].join(':')

			requestAnimationFrame(() => {
				stickyContainer.dataset.sticky = stickyContainer.dataset.sticky.replace(
					'yes-start',
					'yes-end'
				)

				setTimeout(() => {
					stickyContainer.dataset.sticky = stickyContainer.dataset.sticky.replace(
						'yes-end',
						'yes'
					)
				}, 200)
			})
		}

		setTransparencyFor(stickyContainer, 'no')
		document.body.removeAttribute('style')
	} else {
		if (!isSticky) {
			stickyContainer.dataset.sticky = stickyComponents
				.filter((c) => c !== 'yes-end')
				.join(':')

			Array.from(
				stickyContainer.querySelectorAll('[data-row]')
			).map((row) => row.removeAttribute('style'))
			setTransparencyFor(stickyContainer, 'yes')

			document.body.style.setProperty(
				'--header-sticky-height-animated',
				`0px`
			)

			prevScrollY = window.scrollY
			return
		}

		if (
			stickyContainer.dataset.sticky.indexOf('yes-hide') === -1 &&
			stickyContainer.dataset.sticky.indexOf('yes:') > -1 &&
			window.scrollY - prevScrollY > 5
		) {
			stickyContainer.dataset.sticky = [
				'yes-hide-start',
				...stickyComponents,
			].join(':')

			document.body.style.setProperty(
				'--header-sticky-height-animated',
				`0px`
			)

			requestAnimationFrame(() => {
				stickyContainer.dataset.sticky = stickyContainer.dataset.sticky.replace(
					'yes-hide-start',
					'yes-hide-end'
				)

				setTimeout(() => {
					stickyContainer.dataset.sticky = stickyComponents.join(':')

					Array.from(
						stickyContainer.querySelectorAll('[data-row]')
					).map((row) => row.removeAttribute('style'))
					setTransparencyFor(stickyContainer, 'yes')
				}, 200)
			})
		}
	}

	prevScrollY = window.scrollY
}
