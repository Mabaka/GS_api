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
                res.fields.forEach(field => {                    
                    $(`#${field}`)[0].classList.add("err");
                    $(`#e${field}`)[0].innerHTML = "Введенные данные некорректны";
                });
            },
            error: (err,exception)=>{
                console.log(err);
            }
        })
    })
});