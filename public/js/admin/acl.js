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
	    	horizontalAlignment: 'center'
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
		}]
	});

	var chart = $(".barsChart").dxChart({
	    dataSource: dataSource,
		rotated: true,
	    commonSeriesSettings: {
	        argumentField: "nivel",
	        type: "bar"
		},
		title: {
			text: 'Usuários por nível'
		},
	    series: {
			valueField: "val", 
			name: "breeds",
			color: "#069"
		},
	    legend: {
			visible: false,     
	    },
		onPointClick: function(e) {
			var point = e.target;
			point.isSelected() ? point.clearSelection(): point.select();
		}
	}).dxChart("instance");
});