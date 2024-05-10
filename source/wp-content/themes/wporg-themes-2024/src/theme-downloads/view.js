/* global google, jQuery */

async function renderChart( element ) {
	const { theme = '', labelDate, labelValue } = element.dataset;
	const url = 'https://api.wordpress.org/stats/themes/1.0/downloads.php?slug=' + theme + '&limit=260&callback=?';
	const downloads = await jQuery.getJSON( url );

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

		new google.visualization.ColumnChart( element ).draw( data, {
			colors: [ '#3858e9' ],
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
					color: '#1e1e1e',
					fontSize: 10,
				},
			},
			vAxis: {
				format: '###,###',
				textPosition: 'out',
				viewWindowMode: 'explicit',
				viewWindow: { min: 0 },
				textStyle: {
					color: '#1e1e1e',
					fontSize: 14,
				},
				gridlines: {
					color: '#d9d9d9',
				},
				minorGridlines: {
					color: '#f6f6f6',
				},
			},
			tooltip: {
				textStyle: {
					color: '#1e1e1e',
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
