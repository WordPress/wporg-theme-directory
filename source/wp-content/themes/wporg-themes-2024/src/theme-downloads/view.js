/* global google, jQuery */

async function renderChart( element ) {
	const { theme = '', labelDate, labelValue } = element.dataset;
	const url = 'https://api.wordpress.org/stats/themes/1.0/downloads.php?slug=' + theme + '&limit=260&callback=?';
	let downloads = {};
	try {
		downloads = await jQuery.ajax( {
			dataType: 'jsonp',
			url: url,
		} );
	} catch ( err ) {
		return;
	}

	google.charts.setOnLoadCallback( function () {
		const data = new google.visualization.DataTable();
		let count = 0;

		data.addColumn( 'string', labelDate );
		data.addColumn( 'number', labelValue );

		Object.entries( downloads ).forEach( ( [ key, value ] ) => {
			data.addRow();
			data.setValue( count, 0, new Date( key ).toLocaleDateString() );
			data.setValue( count, 1, Number( value ) );
			count++;
		} );

		const bodyStyle = window.getComputedStyle( document.body );
		const colors = {
			'charcoal-1': bodyStyle.getPropertyValue( '--wp--preset--color--charcoal-1' ),
			'blueberry-1': bodyStyle.getPropertyValue( '--wp--preset--color--blueberry-1' ),
			'light-grey-1': bodyStyle.getPropertyValue( '--wp--preset--color--light-grey-1' ),
			'light-grey-2': bodyStyle.getPropertyValue( '--wp--preset--color--light-grey-2' ),
		};

		new google.visualization.ColumnChart( element ).draw( data, {
			colors: [ colors[ 'blueberry-1' ] ],
			fontName: 'var(--wp--preset--font-family--inter)',
			legend: {
				position: 'none',
			},
			chartArea: {
				height: 320,
				bottom: 60,
				left: 60,
				width: '100%',
			},
			hAxis: {
				textStyle: {
					color: colors[ 'charcoal-1' ],
					fontSize: 10,
				},
			},
			vAxis: {
				format: '###,###',
				textPosition: 'out',
				viewWindowMode: 'explicit',
				viewWindow: { min: 0 },
				textStyle: {
					color: colors[ 'charcoal-1' ],
					fontSize: 14,
				},
				gridlines: {
					color: colors[ 'light-grey-1' ],
				},
				minorGridlines: {
					color: colors[ 'light-grey-2' ],
				},
			},
			tooltip: {
				textStyle: {
					color: colors[ 'charcoal-1' ],
				},
			},
			bar: {
				groupWidth: data.getNumberOfRows() > 100 ? '100%' : null,
			},
			height: 390,
		} );
	} );
}

const init = () => {
	google.charts.load( 'current', {
		packages: [ 'corechart' ],
	} );

	const blockElements = document.querySelectorAll( '.wp-block-wporg-theme-downloads' );
	if ( ! blockElements.length ) {
		return;
	}
	blockElements.forEach( renderChart );
};

document.addEventListener( 'DOMContentLoaded', init );
