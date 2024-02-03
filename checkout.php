<script src="https://www.paypalobjects.com/api/checkout.js"></script>
<?php
$total = 0;
$qry = $conn->query("SELECT c.*,p.title,i.price,p.id as pid from `cart` c inner join `inventory` i on i.id=c.inventory_id inner join products p on p.id = i.product_id where c.client_id = " . $_settings->userdata('id'));
while ($row = $qry->fetch_assoc()) :
    $total += $row['price'] * $row['quantity'];
endwhile;
?>
<section class="py-5">
    <div class="container">
        <div class="card rounded-0">
            <div class="card-body"></div>
            <h3 class="text-center"><b>الدفع</b></h3>
            <hr class="border-dark">
            <form action="" id="place_order">
                <input type="hidden" name="amount" value="<?php echo $total ?>">
                <input type="hidden" name="payment_method" value="cod">
                <input type="hidden" name="paid" value="0">
                <div class="row row-col-1 justify-content-center">
                    <div class="col-6">
                        <div class="form-group col mb-0">
                            <label for="" class="control-label">نوع الطلب</label>
                        </div>
                        <div class="form-group d-flex pl-2">
                            <div class="custom-control custom-radio">
                                <input class="custom-control-input custom-control-input-primary" type="radio" id="customRadio4" name="order_type" value="2" checked="">
                                <label for="customRadio4" class="custom-control-label">ورقي</label>
                            </div>
                            <div class="custom-control custom-radio ml-3">
                                <input class="custom-control-input custom-control-input-primary custom-control-input-outline" type="radio" id="customRadio5" name="order_type" value="1">
                                <label for="customRadio5" class="custom-control-label">PDF</label>
                            </div>
                        </div>
                        <div class="form-group col">
                            <label for="phone_number" class="control-label">يرجي تحويل المبلغ عبر فودافون كاش علي هذا الرقم 01067028920
                                المبلغ لا يحتوي علي خدمه التوصيل لو الطلب ورقي "خدمه التوصيل من 40 ل 70 حسب المحافظه وتدفع عند الاستلام"
                            </label>
                            <input type="text" class="form-control" id="phone_number" name="phone_number" placeholder="أدخل رقم الهاتف">
                        </div>
                        <div class="form-group col address-holder">
                            <label for="" class="control-label"> يرجي ارسال سكرين شوت الدفع علي ibdae.katib@gmail.com وسيم ارسال ال الكتاب PDF علي نفس الميل الذي تم الارسال منه </label>
                            <textarea id="" cols="30" rows="3" name="delivery_address" class="form-control" style="resize:none"><?php echo $_settings->userdata('default_delivery_address') ?></textarea>
                        </div>

                        <div class="col">
                            <span>
                                <h4><b>:المجموع</b> <?php echo number_format($total) ?></h4>
                            </span>
                        </div>
                        <hr>
                        <div class="col my-3">
                            <h4 class="text-muted">طريقة الدفع </h4>
                            <div class="d-flex w-100 justify-content-between">
                                <button class="btn btn-flat btn-success" onclick="payment_fawry()">الدفع عبر فودافون كاش</button>
                            </div>
                        </div>

                    </div>
                </div>
        </div>
        </form>
    </div>
    </div>
</section>
<script>
    function payment_fawry() {
        var phoneNumber = $('#phone_number').val();
        if (phoneNumber.trim() === '') {
            alert_toast("يرجى تحويل المبلغ على فودافون كاش على رقم 01067028920 ثم إدخال رقم الهاتف الذي تم التحويل منه", "warning");
            return;
        }

        // تحديث قيم العنوان ورقم الهاتف
        $('[name="delivery_address"]').val($('#delivery_address').val());
        $('[name="phone_number"]').val($('#phone_number').val());

        $('[name="payment_method"]').val("fawry"); // تغيير القيمة لتكون "fawry"
        $('[name="paid"]').val(0); // تحديد أن الدفع لم يتم بعد
        $('#place_order').submit(); // إرسال النموذج
    }

    $(function() {
        $('[name="order_type"]').change(function() {
            if ($(this).val() == 2) {
                $('.address-holder').hide('slow');
            } else {
                $('.address-holder').show('slow');
            }
        });

        $('#place_order').submit(function(e) {
            e.preventDefault()
            $('[name="delivery_address"]').val($('#delivery_address').val());
            $('[name="phone_number"]').val($('#phone_number').val());
            start_loader();
            $.ajax({
                url: 'classes/Master.php?f=place_order',
                method: 'POST',
                data: $(this).serialize(),
                dataType: "json",
                error: err => {
                    console.log(err)
                    alert_toast("تم حجز الأوردر بنجاح", "error")
                    end_loader();
                },
                success: function(resp) {
                    if (!!resp.status && resp.status == 'success') {
                        alert_toast("تم حجز الأوردر بنجاح", "success")
                        setTimeout(function() {
                            location.replace('./')
                        }, 2000)
                    } else {
                        console.log(resp)
                        alert_toast("تم حجز الأوردر بنجاح", "error")
                        end_loader();
                    }
                }
            });
        });
    })
</script>