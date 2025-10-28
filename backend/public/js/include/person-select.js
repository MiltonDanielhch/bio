var personSelected;
        $(document).ready(function(){
            $('#select-person_id').select2({
                placeholder: '<i class="fa fa-search"></i> Buscar...',
                escapeMarkup : function(markup) {
                    return markup;
                },
                language: {
                    inputTooShort: function (data) {
                        return `Por favor ingrese ${data.minimum - data.input.length} o más caracteres`;
                    },
                    noResults: function () {
                        return `<i class="far fa-frown"></i> No hay resultados encontrados`;
                    }
                },
                quietMillis: 250,
                minimumInputLength: 2,
                ajax: {
                    // url: "{{ url('admin/ajax/personList') }}",        
                    url: window.personListUrl, // Usa la variable global
                    processResults: function (data) {
                        let results = [];
                        data.map(data =>{
                            results.push({
                                ...data,
                                disabled: false
                            });
                        });
                        return {
                            results
                        };
                    },
                    cache: true
                },
                templateResult: formatPersonResult,
                templateSelection: (opt) => {
                    window.personSelected = opt; // Guarda en variable global
                    // personSelected = opt;
                    return opt.first_name?opt.first_name+' '+ (opt.middle_name?opt.middle_name+' ':'')+opt.paternal_surname+(opt.maternal_surname?' '+opt.maternal_surname:''):'<i class="fa fa-search"></i> Buscar... ';
                }
            }).change(function(){
                if(window.personSelected){
                    $('#input-dni').val(window.personSelected.ci ? window.personSelected.ci : '');
                }
            });
        });

        function formatPersonResult(option){
            // Si está cargando mostrar texto de carga
            if (option.loading) {
                return '<span class="text-center"><i class="fas fa-spinner fa-spin"></i> Buscando...</span>';
            }

            let image = window.defaultImage;
            
            if(option.image){
                image = window.storagePath+option.image.replace('.', '-cropped.');
            }

            // let image = option.image 
            //     ? `${window.storagePath}${option.image.replace('.', '-cropped.')}`
            //     : window.defaultImage;
            return $(`  
                        <div style="display: flex">
                            <div style="margin: 0px 10px">
                                <img src="${image}" width="50px" />
                            </div>
                            <div>
                                <small>CI: </small><b style="font-size: 15px; color: black">${option.ci?option.ci:'No definido'}</b><br>
                                <b style="font-size: 15px; color: black">${option.first_name} ${option.middle_name?option.middle_name:''} ${option.paternal_surname} ${option.maternal_surname?option.maternal_surname:''}</b>
                            </div>
                        </div>
                        `);
        }