$(document).ready(() => {
    const element = $('#tel')[0];
    const maskOptions = {
        mask: '+{7}(000)000-00-00'
    };
    const mask = IMask(element, maskOptions);

    $('#send').click(()=>{
        const name = $('#name');
        const email = $('#email');
        const tel = $('#tel');
        const date = new Date().toLocaleString();                           
        
        $('.emsg').each((i,obj)=>{
            obj.innerHTML = "";
        });

        $('.input').each((i,obj)=>{
            obj.classList.remove('err');
        });

        $('#ecommon')[0].classList.remove('err');
        $('#ecommon')[0].innerHTML = "";
    
        $.ajax({
            type:"post",
            url:"handler.php",
            dataType: "json",
            data:{
                name:name.val(),
                tel:tel.val(),
                email:email.val(),
                date:date
            },
            success: (res)=>{                   
                if (res.status == "field_error"){
                    res.fields.forEach(field => {                    
                        $(`#${field}`)[0].classList.add("err");
                        $(`#e${field}`)[0].innerHTML = "Введенные данные некорректны";
                    });
                } 
                
                if(res.status == "server_error"){
                    $('#ecommon')[0].classList.add("err");
                    $('#ecommon')[0].innerHTML = "Ошибка на стороне сервера.";    
                }
            },
            error: (err,exception)=>{
                $('#ecommon')[0].classList.add("err");
                $('#ecommon')[0].innerHTML = "Ошибка на стороне сервера.";
            }
        })
    })
});