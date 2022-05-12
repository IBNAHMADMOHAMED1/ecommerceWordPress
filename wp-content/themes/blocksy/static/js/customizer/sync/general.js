wp.customize('content_link_type', (val) =>
	val.bind((to) => (document.body.dataset.link = to))
)

wp.customize('left_right_wide', (val) => {
	val.bind((to) => {
		const els = Array.from(
			document.querySelectorAll(
				'.entry-content > * > .alignleft, .entry-content > * > .alignright'
			)
		)

		els.map((el) =>
			el.parentNode.classList.remove(
				'align-wrap-left',
				'align-wrap-right'
			)
		)

		if (to === 'yes') {
			els.map((el) => {
				if (el.classList.contains('alignleft')) {
					el.parentNode.classList.add('align-wrap-left')
				}

				if (el.classList.contains('alignright')) {
					el.parentNode.classList.add('align-wrap-right')
				}
			})
		}
	})
})

wp.customize('quantity_type', (val) => {
	val.bind((to) => {
		const els = Array.from(
			document.querySelectorAll('.quantity[data-type]')
		)

		els.map((el) => {
			el.classList.add('ct-disable-transitions')

			setTimeout(() => {
				el.dataset.type = to

				setTimeout(() => {
					el.classList.remove('ct-disable-transitions')
				}, 1000)
			}, 100)
		})
	})
})
