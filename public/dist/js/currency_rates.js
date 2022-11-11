
    $(document).ready(async function(){

                var currencies = sessionStorage.getItem('currencies_list');

                if(currencies!=null){

                    setDropDownCurrencies();

                }else{

                   await get_currencies();
                    setDropDownCurrencies();
                    
                }
                
    });



    //--------------------------------------------------------------------------------------------------
    
    // this function will set the currencies got from sessionstorge into to the currency dropdown

    function setDropDownCurrencies(){

        var currencies = sessionStorage.getItem('currencies_list');

        var currency_symbol = sessionStorage.getItem('symbol');

        var currencies_list = JSON.parse(currencies);

        $('#currency_selector').html('');

        currencies_list.forEach(e=>{

            if(e.c_symbol==currency_symbol){
    
                $('#currency_selector').append('<option selected value="'+e.id+'" >'+e.c_symbol+' '+e.c_name+'</option>');

            }else{

                $('#currency_selector').append('<option value="'+e.id+'" >'+e.c_symbol+' '+e.c_name+'</option>');

            }

        });

    }

    //--------------------------------------------------------------------------------------------
    // this function will send an api request then get all the available currencies from db and store it on
    // sessionStorage


    function get_currencies(){

        var url = window.location.origin+"/primarymodule/get_currencies";
    
        var currencies = [];

        $.ajax({
            url:url,
            async:false,
            success:function(res){

                var data = res;

                $('#currency_selector').html(''); 

                data.currencies.forEach((e,index) => {

                    var row = {
                        'id':e.id,
                        'c_rate':e.c_rate,
                        'c_symbol':e.c_symbol,
                        'c_name':e.c_name,
                    }


                    currencies[index] = row;
 
                    if(e.id==data.current){
                       
                        sessionStorage.setItem("rate",e.c_rate);
                        sessionStorage.setItem("symbol",e.c_symbol);

                    }
 
                });

                var currencieslist = JSON.stringify(currencies); 

                sessionStorage.setItem('currencies_list',currencieslist);
            
            },
            error:function(err){
              console.log(err);  
            },
        });

    }

       
    // this function will send a rquest to controller containing the user requested
    // currency id and that function will get the currency rate from api, then update database then 
    // update sessionstorage

        function set_currency_rate(){

         preloader();

           var value =  $('#currency_selector').val();

          var url = window.location.origin+"/primarymodule/dropdown_currency_update";
          var url2 =  window.location.origin+"/primarymodule/get_currencies";
            var param = {
                'currency_id':value,
            }

            
            $.ajax({
                url:url,
                data:param,
               // async: false,
                success:function(res){

                    var data = res;

                    sessionStorage.setItem("rate",data.rate);
                    sessionStorage.setItem("symbol",data.symbol);

                    var param2 = {
                        'rate':data.rate,
                        'symbol':data.symbol,
                    }

                    $.ajax({
                        url:url2,
                        data:param2,
                        async: false,
                        success:function(res){
                           // window.location.reload(true);
                           location.reload();
                        },
                    });


        
                },
                error:function(err){

                    console.log(err);
                   // location.reload();

                },
            });

        }    


 // this functio n will change the currency from one type to another       

        finalValue = "";

        function currency_changer(amountparam){

            var rate = Number(sessionStorage.getItem("rate"));
                
            var amount = Number(amountparam);

            var tempamount = Number(amount) * Number(rate);

           // finalValue =  tempamount.toFixed(2);

            finalValue =  tempamount.toFixed(2).replace(/\d(?=(\d{3})+\.)/g, '$&,'); 

            return finalValue;

        }




// this will return the currenct currency symbol

        function currency_symbol(){

            var symbol = sessionStorage.getItem("symbol");

            return symbol;

        }

