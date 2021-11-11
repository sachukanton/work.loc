class productOrder {
    constructor() {
        var $ = jQuery
        this.exist = false
        this.price = 0
        this.weight = 0
        this.quantity = 0
        this.default = null
        this.totalWeight = null
        this.totalMaxWeight = null
        this.init()
    }

    init() {
        if (window.product_info != undefined) {
            this.exist = true
            this.totalWeight = window.product_info.weight != undefined ? (Array.isArray(window.product_info.weight.value) ? parseFloat(window.product_info.weight.value[0]) : parseFloat(window.product_info.weight.value)) : ''
            this.totalMaxWeight = window.product_info.weight != undefined ? parseInt(window.product_info.weight.max) : ''
            this.formatted()
        }
    }

    formatted() {
        if (!this.exist) return
        this.quantity = parseInt(window.product_info.count)
        this.price = parseFloat(window.product_info.price.value)
        this.weight = window.product_info.weight != undefined ? (Array.isArray(window.product_info.weight.value) ? parseFloat(window.product_info.weight.value[0]) : window.product_info.weight.value) : ''
        var weightSauce =  window.product_info.weight != undefined ? (Array.isArray(window.product_info.weight.value) ? parseFloat(window.product_info.weight.value[1]) : '') : ''
        if (Object.keys(window.product_info.composition.default)) {
            for (const [k, v] of Object.entries(window.product_info.composition.default)) {
                if (v) {
                    this.price += parseInt(v) * parseFloat(window.additionally_ingredients.items[k].price)
                    this.weight += parseInt(v) * parseFloat(window.additionally_ingredients.items[k].weight)
                    for (const [s, d] of Object.entries(window.additionally_ingredients.default)) {
                        if (d.id == k) this.default = d
                    }
                }
            }
        }
        if (Object.keys(window.product_info.composition.ingredients)) {
            for (const [k, v] of Object.entries(window.product_info.composition.ingredients)) {
                if (v && v > 1) {
                    var t = parseInt(v) - 1
                    this.price += t * parseFloat(window.additionally_ingredients.items[k].price)
                    this.weight += t * parseFloat(window.additionally_ingredients.items[k].weight)
                } else if (v == 0) {
                    this.weight -= parseFloat(window.additionally_ingredients.items[k].weight)
                }
            }
        }
        if (Object.keys(window.product_info.composition.additions)) {
            for (const [k, v] of Object.entries(window.product_info.composition.additions)) {
                if (v) {
                    this.price += parseInt(v) * parseFloat(window.additionally_ingredients.items[k].price)
                    this.weight += parseInt(v) * parseFloat(window.additionally_ingredients.items[k].weight)
                }
            }
        }
        this.totalWeight = this.weight
        var w = weightSauce ? (this.weight * this.quantity) + '/' + (weightSauce * this.quantity) : this.weight * this.quantity
        var pv = window.product_info.price.label.replace(':price', this.price * this.quantity);
        var wv = window.product_info.weight != undefined ? window.product_info.weight.label.replace(':weight', w) : '';
        var ad = this.default ? '+' + this.default.title : '';
        $('#shop-product-action-box .real-old, #shop-product-total-box').html(pv);
        $('#shop-product-action-box .product-additional-default').html(ad);
        $('#shop-product-weight-box').html(wv);
    }

    setCount(v) {
        if (!this.exist) return
        window.product_info.count = parseInt(v)
        this.formatted()
    }

    setDefault(d) {
        this.default = null
        if (d) {
            var w = parseFloat(window.additionally_ingredients.items[d].weight)
            if (this.validWeight(w)) return this.message()
        }
        if (Object.keys(window.product_info.composition.default)) {
            for (const [k, v] of Object.entries(window.product_info.composition.default)) {
                window.product_info.composition.default[k] = (d && k == d ? 1 : 0)
                if (d && k == d) {
                    for (const [s, f] of Object.entries(window.additionally_ingredients.default)) {
                        if (f.id == d) this.default = f
                    }
                }
            }
        }
        this.formatted()
        return true
    }

    setIngredient(i, t, g) {
        var g = g || 0
        var c = parseInt(window.product_info.composition.ingredients[i])
        var d = null
        for (const [k, f] of Object.entries(window.additionally_ingredients.ingredients)) {
            if (f.id == i) d = f
        }
        if (g && t == 'increment') {
            c = 1
        } else if (g && t == 'decrement') {
            c = 0
        } else {
            if (t == 'increment') {
                c += 1
            } else {
                c -= 1;
                if (c < 0) c = 0
            }
        }
        if (c > 2) c = 2
        var k = t == 'increment' ? 1 : 0
        var w = parseFloat(window.additionally_ingredients.items[i].weight)
        if (this.validWeight(w, k)) return this.message()
        window.product_info.composition.ingredients[i] = c
        $('#product-order-ingredient-' + i + '-row .product-order-weight').text(c * parseFloat(d.weight))
        $('#product-order-ingredient-' + i + '-row .input-quantity').text(c)
        if (c) {
            $('#product-order-ingredient-' + i + '-row button[name="decrement"]').removeAttr('disabled')
            $('#product-order-ingredient-' + i + '-row input[type="checkbox"]').prop('checked', true)
            $('#product-order-ingredient-' + i).removeClass('not-chosen')
            if (c == 2) {
                $('#product-order-ingredient-' + i + '-row button[name="increment"]').attr('disabled', 'disabled')
            } else {
                $('#product-order-ingredient-' + i + '-row button[name="increment"]').removeAttr('disabled')
            }
        } else {
            $('#product-order-ingredient-' + i + '-row input[type="checkbox"]').prop('checked', false)
            $('#product-order-ingredient-' + i + '-row button[name="decrement"]').attr('disabled', 'disabled')
            $('#product-order-ingredient-' + i + '-row').addClass('not-chosen')
            if (g) $('#product-order-ingredient-' + i + '-row button[name="increment"]').removeAttr('disabled')
        }
        this.formatted()
        return true
    }

    setAdditions(i, t, g) {
        var g = g || 0
        var c = parseInt(window.product_info.composition.additions[i])
        var d = null
        for (const [k, f] of Object.entries(window.additionally_ingredients.additions)) {
            if (f.id == i) d = f
        }
        if (g && t == 'increment') {
            c = 1
        } else if (g && t == 'decrement') {
            c = 0
        } else {
            if (t == 'increment') {
                c += 1
            } else {
                c -= 1;
                if (c < 0) c = 0
            }
        }
        if (c > 2) c = 2
        var k = t == 'increment' ? 1 : 0
        var w = parseFloat(window.additionally_ingredients.items[i].weight)
        if (this.validWeight(w, k)) return this.message()
        window.product_info.composition.additions[i] = c
        $('#product-order-ingredient-' + i + '-row .product-order-weight').text(c * parseFloat(d.weight))
        $('#product-order-ingredient-' + i + '-row .input-quantity').text(c)
        if (c) {
            $('#product-order-ingredient-' + i + '-row input[type="checkbox"]').prop('checked', true)
            $('#product-order-ingredient-' + i + '-row button[name="decrement"]').removeAttr('disabled')
            $('#product-order-ingredient-' + i).removeClass('not-chosen')
            if (c == 2) {
                $('#product-order-ingredient-' + i + '-row button[name="increment"]').attr('disabled', 'disabled')
            } else {
                $('#product-order-ingredient-' + i + '-row button[name="increment"]').removeAttr('disabled')
            }
        } else {
            $('#product-order-ingredient-' + i + '-row input[type="checkbox"]').prop('checked', false)
            $('#product-order-ingredient-' + i + '-row button[name="decrement"]').attr('disabled', 'disabled')
            $('#product-order-ingredient-' + i + '-row').addClass('not-chosen')
            if (g) $('#product-order-ingredient-' + i + '-row button[name="increment"]').removeAttr('disabled')
        }
        this.formatted()
    }

    validWeight(w, t) {
        var t = t == undefined ? 1 : t
        return (t ? (parseFloat(this.totalWeight) + parseFloat(w)) : (parseFloat(this.totalWeight) - parseFloat(w))) > parseInt(this.totalMaxWeight)
    }

    message() {
        var c = window.product_info.weight.message
        if (typeof UIkit == "function") {
            var m = UIkit.modal(('<div class="uk-modal uk-flex-top" id="modal-additionally-ingredients-message"><div class="uk-modal-dialog uk-margin-auto-vertical"><button class="uk-modal-close-outside" type="button" uk-close></button><div class="uk-text-center uk-padding"><div class="message">' + c + '</div></div></div></div>'), {});
            m.show();
            $('body').delegate(`#modal-additionally-ingredients-message`, 'hidden', function (event) {
                if (event.target === event.currentTarget) m.$destroy(true);
            })
        } else {
            alert(c)
        }
    }

    clear() {
        if (!this.exist) return;
        this.default = null
        if (window.product_info != undefined) {
            window.product_info.count = 1
            $('.uk-input-number-counter-box input[type="number"]').val(1)
            window.product_info.spicy.value = window.product_info.spicy.default
            if (window.product_info.spicy.default !== '') {
                var d = Number(window.product_info.spicy.default)
                var b = $('.product-spicy-button')
                if (b.length) {
                    var t = b.data('button')
                    b.data('spicy', d)
                    b.text(t[(d ? 'is_spicy' : 'not_spicy')])
                }
            }
            if (Object.keys(window.product_info.composition.default)) {
                for (const [k, v] of Object.entries(window.product_info.composition.default)) {
                    window.product_info.composition.default[k] = 0
                }
                $('.product-order-default-box input[type="checkbox"]').prop('checked', false)
            }
            if (Object.keys(window.product_info.composition.ingredients)) {
                for (const [k, v] of Object.entries(window.product_info.composition.ingredients)) {
                    window.product_info.composition.ingredients[k] = 1
                }
            }
            if (Object.keys(window.product_info.composition.additions)) {
                for (const [k, v] of Object.entries(window.product_info.composition.additions)) {
                    window.product_info.composition.additions[k] = 0
                }
            }
            if (Object.keys(window.additionally_ingredients.ingredients)) {
                for (const [k, v] of Object.entries(window.additionally_ingredients.ingredients)) {
                    $('#product-order-ingredient-' + v.id + '-row .product-order-weight').text(parseFloat(v.weight))
                }
                $('.product-order-ingredients-box input[type="checkbox"]').prop('checked', true)
                $('.product-order-ingredients-box .input-quantity').text(1)
                $('.product-order-ingredients-box button[name="decrement"]').removeAttr('disabled')
            }
            if (Object.keys(window.additionally_ingredients.additions)) {
                for (const [k, v] of Object.entries(window.additionally_ingredients.additions)) {
                    $('#product-order-ingredient-' + v.id + '-row .product-order-weight').text(parseFloat(v.weight))
                }
                $('.product-order-additions-box input[type="checkbox"]').prop('checked', false)
                $('.product-order-additions-box .input-quantity').text(0)
                $('.product-order-additions-box button[name="decrement"]').attr('disabled', 'disabled')
            }
        }
        this.formatted()
    }
}

var PO = new productOrder()

function useProductOrder($) {
    $('body').delegate('.uk-input-number-counter-box input[type="number"]', 'change', function (event) {
        var t = $(this)
        PO.setCount(t.val())
    });
    $('body').delegate('.product-order-default-box input[type="checkbox"]', 'change', function (event) {
        var t = $(this)
        var v = t.val()
        var s = t.prop('checked')
        var f = PO.setDefault((s ? v : 0))
        if (f === true) {
            $('.product-order-default-box input[type="checkbox"]').prop('checked', false)
            if (s) $('.product-order-default-box input[type="checkbox"][value="' + v + '"]').prop('checked', true)
        } else {
            $('.product-order-default-box input[type="checkbox"][value="' + v + '"]').prop('checked', false)
        }
    });
    $('body').delegate('.product-order-ingredients-box button', 'touch click', function (event) {
        var t = $(this)
        var n = $(this).attr('name')
        var i = $(this).data('ingredient')
        PO.setIngredient(i, n)
    });
    $('body').delegate('.product-order-ingredients-box input[type="checkbox"]', 'change', function (event) {
        var t = $(this)
        var v = t.val()
        var s = t.prop('checked')
        var f = PO.setIngredient(v, (s ? 'increment' : 'decrement'), 1)
        if (f !== true) {
            var c = parseInt(window.product_info.composition.ingredients[v])
            if (c == 0) t.prop('checked', false)
        }
    });
    $('body').delegate('.product-order-additions-box button', 'touch click', function (event) {
        var t = $(this)
        var n = $(this).attr('name')
        var i = $(this).data('ingredient')
        PO.setAdditions(i, n)
    });
    $('body').delegate('.product-order-additions-box input[type="checkbox"]', 'change', function (event) {
        var t = $(this)
        var v = t.val()
        var s = t.prop('checked')
        var f = PO.setAdditions(v, (s ? 'increment' : 'decrement'), 1)
        if (f !== true) {
            var c = parseInt(window.product_info.composition.additions[v])
            if (c == 0) t.prop('checked', false)
        }
    });
    $('body').delegate('#shop-product-buy-button', 'click touch', function (event) {
        event.preventDefault()
        event.stopPropagation()
        var p = $(this).data('path')
        var i = window.product_info;
        var c = {
            default: [],
            additions: [],
            ingredients: []
        }
        if (Object.keys(i.composition.default)) {
            for (const [k, v] of Object.entries(i.composition.default)) {
                if (v) {
                    var t = Object.assign(window.additionally_ingredients.items[k])
                    t.quantity = v
                    c.default.push(t)
                }
            }
        }
        if (Object.keys(i.composition.ingredients)) {
            for (const [k, v] of Object.entries(i.composition.ingredients)) {
                if (v && v > 1) {
                    var t = Object.assign(window.additionally_ingredients.items[k])
                    t.quantity = v
                    c.additions.push(t)
                } else if (v == 0) {
                    var t = Object.assign(window.additionally_ingredients.items[k])
                    t.quantity = 0
                    c.ingredients.push(t)
                }
            }
        }
        if (Object.keys(i.composition.additions)) {
            for (const [k, v] of Object.entries(i.composition.additions)) {
                if (v) {
                    var t = Object.assign(window.additionally_ingredients.items[k])
                    t.quantity = v
                    c.additions.push(t)
                }
            }
        }
        _ajax_post($(this), p, {
            quantity: i.count,
            spicy: i.spicy.value,
            composition: c
        })
    });
}

(function ($) {
    useProductOrder($);
    $('body').delegate('a[data-view-product]', 'click touch', function (event) {
        event.preventDefault()
        var t = $(this).data('view-product')
        if (catalogViewProducts[t] != undefined) {
            $('.' + catalogViewProducts[t].dom_id).replaceWith(catalogViewProducts[t].html)
        }
    });
    $('body').delegate('.product-spicy-button', 'click touch', function (event) {
        event.preventDefault()
        var b = $(this).data('button')
        var s = $(this).data('spicy')
        if (window.product_info != undefined) {
            window.product_info.spicy.value = Number(!s)
        } else {
            var p = $(this).data('product')
            $('.btn-cart-product-' + p).data('spicy', Number(!s))
        }
        $(this).data('spicy', Number(!s))
        $(this).text(b[(s ? 'not_spicy' : 'is_spicy')])
    });
})(jQuery)
