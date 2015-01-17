$(function(){
   var $fileExcel = $(':file[name=excel]'),
       $fakeFileExcel = $('#fake-file-excel');

    $fileExcel.hide().change(function(){
       $fakeFileExcel.html($(this).val());
    });

    $fakeFileExcel.click(function(){
        $fileExcel.trigger('click');
    });

});