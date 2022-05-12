import {
	Fragment,
	createElement,
	useRef,
	useEffect,
	useMemo,
	useCallback,
	useState,
} from '@wordpress/element'

import classnames from 'classnames'

let GradientPickerComponent = () => null

setTimeout(() => {
	if (window.wp.components) {
		import('./gradient/index').then((res) => {
			GradientPickerComponent = res.default
		})
	}
}, 500)

const GradientPicker = ({ value, onChange }) => {
	const allGradients = (window.ct_customizer_localizations ||
		window.ct_localizations)['gradients']

	// let GradientPickerComponent =
	// StableGradientPicker || ExperimentalGradientPicker

	return (
		<Fragment>
			<GradientPickerComponent
				value={value.gradient || ''}
				onChange={(val) => {
					onChange({
						...value,
						gradient: val,
					})
				}}
			/>

			<ul className={'ct-gradient-swatches'}>
				{allGradients.map(({ gradient, slug }) => (
					<li
						onClick={() => {
							onChange({
								...value,
								gradient:
									value.gradient === gradient ? '' : gradient,
							})
						}}
						className={classnames({
							active: gradient === value.gradient,
						})}
						style={{
							'--background-image': gradient,
						}}
						key={slug}></li>
				))}
			</ul>
		</Fragment>
	)
}

export default GradientPicker
