var myPalette = ['#7CBAB4', '#92C7E2', '#75B5D6', '#B78C9B', '#F2CA84', '#A7CA74'];
DevExpress.viz.core.registerPalette('mySuperPalette', myPalette);

$(function(){
	var dataSource = [
	    {nivel: "Aluno", val: 10},
	    {nivel: "Produção", val: 0},
	    {nivel: "Financeiro", val: 5},
	    {nivel: "Cliente", val: 0},
	    {nivel: "Recepção", val: 16},
	    {nivel: "Impressão", val: 15},
	    {nivel: "Operador", val: 12},
	    {nivel: "Administração CV", val: 12}
	];

	$(".pieChart").dxPieChart({
	    dataSource: dataSource,
	    title: {
	    	text: 'Permissões por nível',
	    	horizontalAlignment: 'center',
	    	font: {
	    		family: 'Roboto',
	    		size: 20,
	    		color: '#555555'
	    	}
	    },
		tooltip: {
			enabled: true,
			customizeText: function() { 
				return this.valueText + " - " + this.percentText + ' de acesso';
			}
		},
		legend: {
            horizontalAlignment: 'center',
            verticalAlignment: 'bottom'
        },
		series: [{
			type: "doughnut",
			argumentField: "nivel",
			label: {
				visible: true,
				connector: {
					visible: true
				}
			}
		}],
		palette: 'mySuperPalette'
	});

	var chart = $(".barsChart").dxChart({
	    dataSource: dataSource,
	    commonSeriesSettings: {
	        argumentField: "nivel",
	        type: "bar"
		},
		title: {
			text: 'Usuários por nível',
			font: {
	    		family: 'Roboto',
	    		size: 20,
	    		color: '#555555'
	    	}
		},
		tooltip: {
			enabled: true,
			customizeText: function() { 
				return this.valueText + " usuários";
			}
		},
	    series: {
			valueField: "val", 
			name: "nivel",
			color: '#E6A759'
		},
	    legend: {
			visible: false,     
	    }
	}).dxChart("instance");
});