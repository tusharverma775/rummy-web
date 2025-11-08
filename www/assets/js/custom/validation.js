/*
 * Form Validation
 */
$(function () {

    $('#add_phamracy').validate({
        rules: {
            phamracy_name: {
                required: true
            },
            contact_person: {
                required: true
            },
            email_id: {
                required: true,
                remote: {
                    url: BASE_URL + 'backend/Ajax/CheckExisting',
                    type: 'post',
                    data: {
                        'id': $('[name="id"]').val(),
                        'type': $('[name="type"]').val()
                    }
                }
            },
            contact_number: {
                required: true,
                number: true,
                minlength: 10,
                maxlength: 10
            },
            password: {
                required: true
            }
        },
        messages: {
            phamracy_name: {
                required: 'this field is required',
            },
            contact_person: {
                required: 'this field is required'
            },
            email_id: {
                required: 'this field is required',
                remote: 'Email already exists',
            },
            contact_number: {
                required: 'this field is required',
                number: 'only number are allowed',
                minlength: 'invalid number',
                maxlength: 'invalid number'
            },
            password: {
                required: 'this field is required'
            }
        },
        errorElement: 'div',
        errorPlacement: function (error, element) {
            var placement = $(element).data('error')
            if (placement) {
                $(placement).append(error)
            } else {
                error.insertAfter(element)
            }
        }
    })
    $('#add_salesperson').validate({
        rules: {
            contact_person: {
                required: true
            },
            email_id: {
                required: true,
                remote: {
                    url: BASE_URL + 'backend/Ajax/CheckExisting',
                    type: 'post',
                    data: {
                        'id': $('[name="id"]').val(),
                        'type': $('[name="type"]').val()
                    }
                }
            },
            contact_number: {
                required: true,
                number: true,
                minlength: 10,
                maxlength: 10
            },
            password: {
                required: true
            }
        },
        messages: {
            contact_person: {
                required: 'this field is required',
            },
            email_id: {
                required: 'this field is required',
                remote: 'Email already exists',
            },
            contact_number: {
                required: 'this field is required',
                number: 'only number are allowed',
                minlength: 'invalid number',
                maxlength: 'invalid number'
            },
            password: {
                required: 'this field is required'
            }
        },
        errorElement: 'div',
        errorPlacement: function (error, element) {
            var placement = $(element).data('error')
            if (placement) {
                $(placement).append(error)
            } else {
                error.insertAfter(element)
            }
        }
    })
    $('#add_product').validate({
        rules: {
            product_name: {
                required: true
            },
            type:{
                required:true
            },
            mrp:{
                required:true,
                number:true
            },
            manufacturer_name:{
                required:true
            }
        },
        messages: {
            product_name: {
                required: 'this field is required',
            },
            type: {
                required: 'this field is required',
            },
            mrp: {
                required: 'this field is required',
                number:'only numbers are allowed'
            },
            manufacturer_name: {
                required: 'this field is required',
            } 
            
        },
        errorElement: 'div',
        errorPlacement: function (error, element) {
            var placement = $(element).data('error')
            if (placement) {
                $(placement).append(error)
            } else {
                error.insertAfter(element)
            }
        }
    })
    $('#add_stock').validate({
        rules: {
            product: {
                required: true
            },
            quantity:{
                required:true,
                number:true
            },
            mf_date:{
                required:true,
                 
            },
            expiry_date:{
                required:true
            },
            batch_no:{
                required:true
            }
        },
        messages: {
            product: {
                required: 'this field is required',
            },
            quantity: {
                required: 'this field is required',
                number: 'only numbers are allowed'
            },
            mf_date: {
                required: 'this field is required',
                 
            },
            expiry_date: {
                required: 'this field is required',
            },
            batch_no:{
                required: 'this field is required',
            }
            
        },
        errorElement: 'div',
        errorPlacement: function (error, element) {
            var placement = $(element).data('error')
            if (placement) {
                $(placement).append(error)
            } else {
                error.insertAfter(element)
            }
        }
    })
})