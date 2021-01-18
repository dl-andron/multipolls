jQuery(document).ready(function($)
{	
	//один вариант, один вариант либо свой, да или нет
    var field_radio=$("input[type=radio][name^=r],input[type=radio][name^=yn]");
    $.each(field_radio,function(key,el)
    {
        var el_name=$(el).attr("name");
        var el_value=$(el).val();
        if ($.session.get(el_name))
        {
            if (el_value==$.session.get(el_name)) $(el).prop("checked","checked");				
        }
        el.addEventListener("change", function() {       
            el_value=$(el).val();						
            $.session.set(el_name,el_value);
        });
        
    });
    //несколько вариантов, один вариант и свой
    var field_checkbox=$("input[type=checkbox][name^=cb]");
    $.each(field_checkbox,function(key,el)
    {
        var el_name=$(el).attr("name");
        var el_value=$(el).val();
        if ($.session.get(el_name)) 
        {
            el_ses_value=$.session.get(el_name).split(",");
            if ($.inArray(el_value,el_ses_value)>=0) $(el).prop("checked","checked");							
        }
        el.addEventListener("change", function() {
            el_value=[];
            $.each($(field_checkbox).filter(':checked'),function()
            {
                el_value.push($(this).val());
            });					
            $.session.set(el_name,el_value);
        });
    });
    //цифра по шкале
    var field_select=$("select[name^=s]");
    $.each(field_select,function(key,el)
    {
        var el_name=$(el).attr("name");
        var el_value=$(el).val();
        
        if ($.session.get(el_name) ) 
        {
            $.each($(el).find("option"),function()
            {					
                if ($(this).text()==$.session.get(el_name)) $(this).prop("selected","selected");
            });
        }
        el.addEventListener("click", function() {	
            el_value=$(el).val();					
            $.session.set(el_name,$(el).find("option:selected").text());
        });
    });
    //ввод текста, цифра по шкале и ввод текста
    var field_textarea=$("textarea[name^=ta],textarea[name^=sta-text]");
    $.each(field_textarea,function(key,el)
    {
        var el_name=$(el).attr("name");
        var el_value=$(el).val();
        if ($.session.get(el_name))
        {
            $(el).val($.session.get(el_name));	
        }
        el.addEventListener("change", function() {	 
            el_value=$(el).val();					
            $.session.set(el_name,el_value);
        });
        el.addEventListener("paste", function() {	
            el_value=$(el).val();						
            $.session.set(el_name,el_value);
        });
        el.addEventListener("cut", function() {	
            el_value=$(el).val();						
            $.session.set(el_name,el_value);
        });
        el.addEventListener("keyup", function() {	 
            el_value=$(el).val();					
            $.session.set(el_name,el_value);
        });
    });
    //текстовое поле свой ответ - один вариант 
    var field_ro_custom=$("input[type=text][name^=ro]");
    $.each(field_ro_custom,function(key,el)
    {
        var el_name=$(el).attr("name");
        var el_value=$(el).val();
        if ($.session.get(el_name))
        {
            $(el).val($.session.get(el_name));	
        }
        el.addEventListener("change", function() {	 
            el_value=$(el).val();					
            $.session.set(el_name,el_value);
        });
        el.addEventListener("paste", function() {	
            el_value=$(el).val();						
            $.session.set(el_name,el_value);
        });
        el.addEventListener("cut", function() {	
            el_value=$(el).val();						
            $.session.set(el_name,el_value);
        });
        el.addEventListener("keyup", function() {	 
            el_value=$(el).val();					
            $.session.set(el_name,el_value);
        });	
        el.addEventListener("focusin", function() {		
            el_name2=$(el).prev('.own-radio').attr("name");
            el_value2=$(el).prev('.own-radio').val();		
            $.session.set(el_name2,el_value2);
        });
    });
    //текстовое поле свой ответ - несколько вариантов
    var field_cbo_custom=$("input[type=text][name^=cbo]");
    $.each(field_cbo_custom,function(key,el)
    {
        var el_name=$(el).attr("name");
        var el_value=$(el).val();
        if ($.session.get(el_name))
        {
            $(el).val($.session.get(el_name));	
        }
        el.addEventListener("change", function() {	 
            el_value=$(el).val();					
            $.session.set(el_name,el_value);
        });
        el.addEventListener("paste", function() {	
            el_value=$(el).val();						
            $.session.set(el_name,el_value);
        });
        el.addEventListener("cut", function() {	
            el_value=$(el).val();						
            $.session.set(el_name,el_value);
        });
        el.addEventListener("keyup", function() {	 
            el_value=$(el).val();					
            $.session.set(el_name,el_value);
        });	
        el.addEventListener("focusin", function() {		
            el_name2=$(el).prev('.own-checkbox').attr("name");
            el_value2=$(el).prev('.own-checkbox').val();		
            $.session.set(el_name2,el_value2);
        });
    });
    //выбор по приоритету
    var field_priority = $(".priority-list");
    $.each(field_priority,function(key,el) 
    {
        var el_name=$(el).find('[type="hidden"]:first').attr("name");
        var el=$(this);
        
        if ($.session.get(el_name))
        {
            var ordering=$.session.get(el_name).split(","); //сортировка из storage  
            $.each(ordering,function(key,el){
                var element = $("input[type=hidden][name^=priority][value='"+el+"']").closest('li'); 
                element.appendTo(element.parent());
            });    
        }

        el.on("sortupdate", function(event, ui) {
            el_value=[];
            $.each($("input[type=hidden][name='"+el_name+"']"),function()
            {
                el_value.push($(this).val());
            });					
            $.session.set(el_name,el_value);
        });
    }); 
});