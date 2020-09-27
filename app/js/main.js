$(document).ready(function () {
    var msg_error = onlyPoints = routing = false;
    /* ***** Layers **** */
    var layerDesc = {
        base: 'base',
        type: 'maptile',
        scheme: 'normal.day',
        app_id: '1Iumu3rkyzLbzV8I0IrZ',
        app_code: 'kdR9u6rdxO_6bt-NStOwxw'
    };

    function createUrl(tpl, layerDesc) {
        return tpl
            .replace('{base}', layerDesc.base)
            .replace('{type}', layerDesc.type)
            .replace('{scheme}', layerDesc.scheme)
            .replace('{app_id}', layerDesc.app_id)
            .replace('{app_code}', layerDesc.app_code);
    }
    var urlTpl = 'https://{1-4}.{base}.maps.cit.api.here.com' +
        '/{type}/2.1/maptile/newest/{scheme}/{z}/{x}/{y}/256/png' +
        '?app_id={app_id}&app_code={app_code}';
    var hereMap = new ol.layer.Tile({
        preload: Infinity,
        source: new ol.source.XYZ({
            url: createUrl(urlTpl, layerDesc)
        })
    });
    var bing = new ol.layer.Tile({
        preload: Infinity,
        source: new ol.source.BingMaps({
            key: 'AiDNS2D3Ifp-EUFduB_jD_pJNzBcilx-ssrTrfMkJLD8Ml8kDA-HNgFFNpuCqyHd',
            imagerySet: 'Aerial',
        }),
        visible: false
    });
    /* ***** End Layers **** */
    // cria a latitude e longitude e converte para o sistema de projeção padrão
    var aveiro = ol.proj.transform([-8.653898, 40.641854], 'EPSG:4326', 'EPSG:3857');
    // view, começando no center (aveiro)
    var view = new ol.View({
        center: aveiro,
        zoom: 12
    });
    // mapa com o id, layers, controls e view
    var map = new ol.Map({
        target: 'map',
        layers: [bing, hereMap],
        //osm],
        controls: ol.control.defaults({
            attribution: false // remove os atributos por default
        }),
        view: view
    });
    /* events quando o utilizador move o mapa */
    map.on('pointerdrag', function () {
        document.body.style.cursor = 'all-scroll';
    });
    map.on('pointerup', function () {
        document.body.style.cursor = 'auto';
    });
    /* events quando o utilizador move o mapa */
    /* Event onclick no tipo de mapa */
    $("#layer").click(function () {
        if ($("#layer").html() == "Mapa") {
            hereMap.setVisible(false);
            bing.setVisible(true);
            $("#layer").html("Satélite");
        } else {
            bing.setVisible(false);
            hereMap.setVisible(true);
            $("#layer").html("Mapa");
        }
    })
    /* Event onclick no tipo de mapa */
    // Gera os pontos por defeito
    generatePav();
    optionsMunicipios("municipio_pav");
    optionsMod("modalidade_pav");

    var gjson = new ol.format.GeoJSON({});

    var source_points, source_polygon, source_poi, gjson_layer, source_route, points_routing,
        pontoInicial, pontoDestino,
        error_msg = "<div class=\"d-flex h-100 justify-content-center align-items-center\">" +
        "<div class=\"alert alert-danger h-25\">Ocorreu um erro de comunicação. Por favor tente mais tarde</div>" +
        "</div>",
        loading_spinner = "<div class='d-flex h-100 justify-content-center align-items-center'>" +
        "<div class='spinner-border text-blue' role='status'>" +
        "<span class='sr-only'>A carregar...</span>" +
        "</div></div>";

    /* estilo dos pontos no mapa */
    function setIcon(img, scale, size) {
        //Ícone dos pontos
        var pointStyle = new ol.style.Style({
            image: new ol.style.Icon({
                src: img,
                size: [size, size],
                scale: scale,
                anchor: [0.5, 1]
            }),
        });
        return pointStyle;
    };

    // Label dos pontos
    function setText(color) {
        var text = new ol.style.Style({
            fill: new ol.style.Fill({
                color: 'rgba(255, 255, 255, 0.6)'
            }),
            stroke: new ol.style.Stroke({
                color: '#319FD3',
                width: 1
            }),
            text: new ol.style.Text({
                font: 'bold 9px Lato, sans-serif',
                fill: new ol.style.Fill({
                    color: color
                }),
                stroke: new ol.style.Stroke({
                    color: 'white',
                    width: 3,
                }),
            })
        });
        return text;
    }

    // esconde o label numa determinada resolução
    function style(text, img, scale, size, color) {
        var style = function (feature, resolution) {
            var styles = [setIcon(img, scale, size)];
            if (resolution < 25) {
                var t = setText(color);
                t.getText().setText(feature.get(text));
                styles.push(t);
            }
            return styles;
        }
        return style;
    }

    // função que limpa as sources
    function clearLayers() {
        if (source_points) source_points.clear();
        if (source_polygon) source_polygon.clear();
        if (source_poi) source_poi.clear();
        clearRouting();
    }

    function clearRouting() {
        if (source_route) source_route.clear();
        if (pontoInicial) pontoInicial.setGeometry(null);
        if (pontoDestino) pontoDestino.setGeometry(null);
    }

    // adiciona o layer das infraestruturas
    function addPavilhoes(x) {
        source_points = new ol.source.Vector({
            features: gjson.readFeatures(x)
        });
        gjson_layer = new ol.layer.Vector({
            source: source_points,
            style: style('nome', 'img/icon_red.png', 0.030, 1024, 'red')
        });
        map.addLayer(gjson_layer);
    }

    // adiciona o layer dos clubes/Universidade
    function addRepresentacoes(x) {
        source_points = new ol.source.Vector({
            features: gjson.readFeatures(x)
        });
        gjson_layer = new ol.layer.Vector({
            source: source_points,
            style: function (feature, resolution) {
                var img, color, size, scale;
                if (feature.get("tipo") === 'Universidade') {
                    img = "img/icon_ua.png";
                    color = "gray";
                    scale = 0.040;
                    size = 1400;
                } else {
                    img = "img/icon_blue.png";
                    color = "blue";
                    scale = 0.030;
                    size = 1024;
                }
                var styles = [setIcon(img, scale, size)];
                if (resolution < 25) {
                    var t = setText(color);
                    t.getText().setText(feature.get("nome"));
                    styles.push(t);
                }
                return styles;
            }
        });
        map.addLayer(gjson_layer);
    }

    // getPavilhoes
    function generatePav() {
        $.ajax({
            type: 'POST',
            url: 'php/getPavilhoes.php',
            dataType: 'json',
            cache: false,
            success: function (data) {
                if (data.error === 'none') addPavilhoes(data.pavilhoes);
                else {
                    if (!msg_error) {
                        Swal.fire({
                            type: 'error',
                            title: 'Ocorreu um erro de comunicação. Por favor tente mais tarde.',
                        })
                        msg_error = true;
                    }
                }
            },
            error: function () {
                if (!msg_error) {
                    Swal.fire({
                        type: 'error',
                        title: 'Ocorreu um erro de comunicação. Por favor tente mais tarde.',
                    })
                    msg_error = true;
                }
            }
        });
    }

    //getPoi
    function getPoi(x, y) {
        $.ajax({
            type: 'POST',
            url: 'php/getPoi.php',
            dataType: 'json',
            data: {
                myData: JSON.stringify({
                    "x": x,
                    "y": y
                }),
            },
            cache: false,
            success: function (data) {
                if (data.error === 'none') {
                    if (source_poi) source_poi.clear();
                    source_poi = new ol.source.Vector({
                        features: gjson.readFeatures(data.poi)
                    });
                    gjson_layer = new ol.layer.Vector({
                        source: source_poi,
                        style: function (feature, resolution) {
                            var img, color;
                            if (feature.get("categoria") === 'Restaurante') {
                                img = "img/restaurant.png";
                                color = "green";
                            } else if (feature.get("categoria") === 'Café') {
                                img = "img/coffee_cup.png";
                                color = "yellow";
                            } else {
                                img = "img/martini_glass.png";
                                color = "orange";
                            }
                            var styles = [setIcon(img, 0.030, 1024)];
                            if (resolution < 25) {
                                var t = setText(color);
                                t.getText().setText(feature.get("nome"));
                                styles.push(t);
                            }
                            return styles;
                        }
                    });
                    map.addLayer(gjson_layer);
                } else {
                    if (!msg_error) {
                        Swal.fire({
                            type: 'error',
                            title: 'Ocorreu um erro de comunicação. Por favor tente mais tarde.',
                        })
                        msg_error = true;
                    }
                }
            },
            error: function (xhr, status, error) {
                if (!msg_error) {
                    Swal.fire({
                        type: 'error',
                        title: 'Ocorreu um erro de comunicação. Por favor tente mais tarde.',
                    })
                    msg_error = true;
                }
            }
        })
    }

    // getRepresentacoes
    function generateRep() {
        $.ajax({
            type: 'POST',
            url: 'php/getRep.php',
            dataType: 'json',
            cache: false,
            success: function (data) {
                if (data.error === 'none') addRepresentacoes(data.clubes);
                else {
                    if (!msg_error) {
                        Swal.fire({
                            type: 'error',
                            title: 'Ocorreu um erro de comunicação. Por favor tente mais tarde.',
                        })
                        msg_error = true;
                    }
                }
            },
            error: function () {
                if (!msg_error) {
                    Swal.fire({
                        type: 'error',
                        title: 'Ocorreu um erro de comunicação. Por favor tente mais tarde.',
                    })
                    msg_error = true;
                }
            }
        });
    }

    var popup, select, pav = clube = poi = false;

    // evento onclick num ponto qualquer no mapa para o popup, detalhes e POI
    $(map.getViewport()).on('click', function (e) {
        map.removeOverlay(popup);
        map.removeInteraction(select);
        if (routing === false) {
            var pixel = map.getEventPixel(e.originalEvent);
            var hit = map.forEachFeatureAtPixel(pixel, function (feature) {
                if (feature.getGeometry().getType() === 'Polygon' || feature.getGeometry().getType() === 'LineString')
                    return false;
                if (feature.get('tipologia') !== undefined) pav = true;
                else if (feature.get('categoria') !== undefined) poi = true;
                else clube = true;
                return true;
            });
            if (hit) {
                if (pav === true) {
                    // Select interaction
                    select = new ol.interaction.Select({
                        hitTolerance: 5,
                        multi: false,
                        condition: ol.events.condition.singleClick
                    });
                    map.addInteraction(select);
                    popup = new ol.Overlay.PopupFeature({
                        popupClass: 'default anim',
                        select: select,
                        canFix: true,
                        template: {
                            title: function (f) {
                                var locale = f.get('nome'),
                                    coords;
                                coords = f.getGeometry().getCoordinates();
                                legendarr(locale);
                                getPoi(coords[0], coords[1]);
                                return f.get('nome');
                            },
                            attributes: // [ 'freguesia', 'tipologia' ]
                            {
                                'freguesia': {
                                    title: 'Freguesia'
                                },
                                'tipologia': {
                                    title: 'Tipologia'
                                },
                            }
                        }
                    });
                    pav = false;
                } else if (clube === true) {
                    // Select interaction
                    select = new ol.interaction.Select({
                        hitTolerance: 5,
                        multi: false,
                        condition: ol.events.condition.singleClick
                    });
                    map.addInteraction(select);
                    popup = new ol.Overlay.PopupFeature({
                        popupClass: 'default anim',
                        select: select,
                        canFix: true,
                        template: {
                            title: function (f) {
                                var locale = f.get('nome'),
                                    coords;
                                coords = f.getGeometry().getCoordinates();
                                legendarM(locale);
                                getPoi(coords[0], coords[1]);
                                return f.get('nome');
                            },
                            attributes: // [ 'municipio', 'tipo' ]
                            {
                                'municipio': {
                                    title: 'Município'
                                },
                                'tipo': {
                                    title: 'Tipo'
                                },
                            }
                        }
                    });
                    clube = false;
                } else {
                    // Select interaction
                    select = new ol.interaction.Select({
                        hitTolerance: 5,
                        multi: false,
                        condition: ol.events.condition.singleClick
                    });
                    map.addInteraction(select);
                    popup = new ol.Overlay.PopupFeature({
                        popupClass: 'default anim',
                        select: select,
                        canFix: true,
                        template: {
                            title: function (f) {
                                return f.get('nome');
                            },
                            attributes: // [ 'categoria' ]
                            {
                                'categoria': {
                                    title: 'Categoria'
                                }
                            }
                        }
                    });
                    poi = false;
                }
                map.addOverlay(popup);
            }
        }
    });

    // cursor pointer ao mover o cursor por cima de um ponto
    var target = map.getTarget();
    var jTarget = typeof target === "string" ? $("#" + target) : $(target);
    $(map.getViewport()).on('mousemove', function (e) {
        var pixel = map.getEventPixel(e.originalEvent);
        var hit = map.forEachFeatureAtPixel(pixel, function (feature) {
            if (feature.getGeometry().getType() === 'Polygon' || feature.getGeometry().getType() === 'LineString')
                return false;
            return true;
        });
        if (hit) jTarget.css("cursor", "pointer");
        else jTarget.css("cursor", "");
    });

    // função para personalizar o poligono
    function stylePolygon() {
        var p = new ol.style.Style({
            fill: new ol.style.Fill({
                color: [210, 215, 211, 0.4]
            }),
            stroke: new ol.style.Stroke({
                color: 'black',
                width: 2
            })
        });
        return p;
    }

    // gera as freguesias do select
    function optionsFreguesias(m, id) {
        $.ajaxSetup({
            async: false
        });
        $.ajax({
            url: 'php/freguesias.php',
            type: 'POST',
            data: {
                myData: m
            },
            dataType: 'json',
            async: false,
            cache: false,
            success: function (data) {
                if (data.error === "exception")
                    $('#' + id + ' option:first-child').text("Ocorreu um erro");
                else {
                    var txt = "";
                    $.each(data.freguesias, function (i, item) {
                        txt += "<option>" + item.f + "</option>";
                    });
                    $('#' + id).html("<option>Todas</option>" + txt);
                    $('#' + id).prop('disabled', false);
                }
            },
            error: function () {
                $('#' + id + ' option:first-child').text("Ocorreu um erro de comunicação");
            }
        });
        $.ajaxSetup({
            async: true
        });
    }

    // gera os municípios do select
    function optionsMunicipios(x) {
        $.ajax({
            type: 'POST',
            url: 'php/municipios.php',
            dataType: 'json',
            cache: false,
            success: function (data) {
                if (data.error === "exception")
                    $('#' + x + ' option:first-child').text("Ocorreu um erro");
                else {
                    var txt = "";
                    $.each(data.municipios, function (i, item) {
                        txt += "<option>" + item.m + "</option>";
                    });
                    $('#' + x).html("<option>Todos</option>" + txt);
                }
            },
            error: function () {
                $('#' + x + ' option:first-child').text("Ocorreu um erro de comunicação");
            }
        });
    }

    // gera as modalidades do select
    function optionsMod(id) {
        $.ajax({
            type: 'POST',
            url: 'php/modalidades.php',
            dataType: 'json',
            cache: false,
            success: function (data) {
                if (data.error === 'exception') {
                    $('#' + id + ' option:first-child').text("Ocorreu um erro");
                } else {
                    var txt = "";
                    $.each(data.modalidades, function (i, item) {
                        txt += "<option>" + item.nome + "</option>";
                    });
                    $('#' + id).html("<option>Todas</option>" + txt);
                }
            },
            error: function () {
                $('#' + id + ' option:first-child').text("Ocorreu um erro de comunicação");
            }
        });
    }

    // gera as opções dos escalões consoante determinada modalidade
    function optionsEscaloes(m) {
        $.ajaxSetup({
            async: false
        });
        $.ajax({
            url: 'php/escaloes.php',
            type: 'POST',
            data: {
                myData: m
            },
            dataType: 'json',
            async: false,
            cache: false,
            success: function (data) {
                if (data.error === "exception")
                    $('#escalao_pav option:first-child').text("Ocorreu um erro");
                else {
                    if (data.rows === 0) {
                        $('#escalao_clubes').html("<option>Todos</option>");
                        $('#escalao_clubes').prop('disabled', true);
                    } else {
                        if (data.rows === 1) {
                            $('#escalao_clubes').html("<option>" + data.escaloes[0].escalao + "</option>");
                            $('#escalao_clubes').prop('disabled', false);
                        } else if (data.rows === 2) {
                            $('#escalao_clubes').html("<option>Ambos</option>" +
                                "<option>" + data.escaloes[0].escalao + "</option>" +
                                "<option>" + data.escaloes[1].escalao + "</option>");
                            $('#escalao_clubes').prop('disabled', false);
                        } else {
                            var txt = "";
                            $.each(data.escaloes, function (i, item) {
                                txt += "<option>" + item.escalao + "</option>";
                            });
                            $('#escalao_clubes').html("<option>Todos</option>" + txt);
                        }
                        $('#escalao_clubes').prop('disabled', false);
                    }
                }
            },
            error: function () {
                $('#escalao_pav option:first-child').text("Ocorreu um erro de comunicação");
            }
        });
        $.ajaxSetup({
            async: true
        });
    }

    // animação para fazer zoom para os pontos no mapa
    function zoomPointsAnimate() {
        if (onlyPoints) {
            map.getView().animate({
                zoom: 12
            }, {
                center: aveiro
            }, {
                duration: 300
            });
            onlyPoints = false;
        }
    }

    var count = 0;

    // quando clica numa das tabs dos filtros
    $(".tabs li a").click(function () {
        map.removeOverlay(popup);
        map.removeInteraction(select);
        // verifica se a tab não está selecionada
        if (!$(this).hasClass("active")) {
            var h = $(this).attr("href");
            clearLayers();
            if (routing === true) {
                $("#visto").remove();
                $("#pontoPartida").after("<input id='inputPontoInicial' value=' selecione no mapa'></input>");
                $("#visto2").remove();
                $("#pontoChegada").after("<input id='inputPontoFinal' value=' selecione no mapa'></input>");
            }
            if (source_poi) source_poi.clear();
            if (count === 0) {
                optionsMunicipios('municipio_clubes');
                optionsMod('modalidade_clubes');
                generateRep();
                onlyPoints = true;
                zoomPointsAnimate();
                count++;
            } else {
                var mun, freg;
                if (h === '#pavilhoes') {
                    clearLayers();
                    var mod, coberto, preco, json = {};
                    mun = $('#municipio_pav').val();
                    freg = $('#freguesia_pav').val();
                    mod = $('#modalidade_pav').val();
                    coberto = $('#coberto_pav').val();
                    preco = $('#preco_pav').val();
                    if (mun !== 'Todos' && freg === 'Todas') json['municipio'] = mun;
                    else {
                        if (freg !== 'Todas') json['freguesia'] = freg;
                    }
                    if (mod !== 'Todas') json['modalidade'] = mod;
                    if (coberto !== 'Ambos') json['coberto'] = coberto;
                    if (preco !== 'Todos') json['preco'] = preco;
                    filterPav(json);
                } else {
                    var mun, freg, mod, escalao, genero, adaptado, json = {};
                    mun = $('#municipio_clubes').val();
                    freg = $('#freguesia_clubes').val();
                    mod = $('#modalidade_clubes').val();
                    escalao = $('#escalao_clubes').val();
                    genero = $('#genero_clubes').val();
                    adaptado = $('#adaptado_clubes').val();
                    if (mun !== 'Todos' && freg === 'Todas') json['municipio'] = mun;
                    else {
                        if (freg !== 'Todas') json['freguesia'] = freg;
                    }
                    if (mod !== 'Todas' && escalao.length === 1) json['modalidade'] = mod;
                    else {
                        if (escalao !== 'Todos') {
                            json['escalao'] = escalao;
                            json['modalidade'] = mod;
                        }
                    }
                    if (genero !== 'Ambos') json['genero'] = genero;
                    if (adaptado !== 'Ambos') json['adaptado'] = adaptado;
                    filterRep(json);
                }
            }
        }
    });

    // adiciona layer dos limites do munícipio ou freguesia
    function addPolygon(x) {
        var width = window.outerWidth;
        source_polygon = new ol.source.Vector({
            features: gjson.readFeatures(x)
        });
        gjson_layer = new ol.layer.Vector({
            source: source_polygon,
            style: stylePolygon()
        });
        map.addLayer(gjson_layer);
        //faz zoom até ao layer consoante a width da window
        if (width > 947)
            map.getView().fit(source_polygon.getExtent(), {
                duration: 500,
                size: [500, 500],
                padding: [0, 0, 0, 600]
            });
        else
            map.getView().fit(source_polygon.getExtent(), {
                duration: 500,
                size: [500, 500]
            });
    }

    // caso a pessoa pretenda os select das infraestruturas como default
    $("#predefine_pav").click(function () {
        map.removeOverlay(popup);
        map.removeInteraction(select);
        clearLayers();
        generatePav();
        onlyPoints = true;
        zoomPointsAnimate();
        document.getElementById("municipio_pav").selectedIndex = "0";
        $('#freguesia_pav').html('<option>Todas</option>');
        $('#freguesia_pav').prop('disabled', true);
        document.getElementById("modalidade_pav").selectedIndex = "0";
        document.getElementById("coberto_pav").selectedIndex = "0";
        document.getElementById("preco_pav").selectedIndex = "0";
        $("#inputPesquisar").val(" ");
        var local = $("#inputPesquisar").val();
        pesquisar(local);
        routing = false;
        resetRouting();
    });

    // caso a pessoa pretenda os select dos clubes como default
    $("#predefine_clubes").click(function () {
        map.removeOverlay(popup);
        map.removeInteraction(select);
        clearLayers();
        generateRep();
        onlyPoints = true;
        zoomPointsAnimate();
        document.getElementById("municipio_clubes").selectedIndex = "0";
        $('#freguesia_clubes').html('<option>Todas</option>');
        $('#freguesia_clubes').prop('disabled', true);
        document.getElementById("modalidade_clubes").selectedIndex = "0";
        $('#escalao_clubes').html('<option>Todos</option>');
        $('#escalao_clubes').prop('disabled', true);
        document.getElementById("genero_clubes").selectedIndex = "0";
        document.getElementById("adaptado_clubes").selectedIndex = "0";
        $("#inputPesquisar").val(" ");
        var local = $("#inputPesquisar").val();
        pesquisarPClubes(local);
        routing = false;
        resetRouting();
    });

    // chamada ajax para filtrar os dados das infraestruturas
    function filterPav(json) {
        $.ajax({
            url: 'php/filterPav.php',
            type: 'POST',
            data: {
                myData: JSON.stringify(json),
            },
            dataType: 'json',
            cache: false,
            success: function (data) {
                if (data.error === 'none') {
                    clearLayers();
                    if (data.municipio) addPolygon(data.municipio);
                    else if (data.freguesia) addPolygon(data.freguesia);
                    else onlyPoints = true;
                    addPavilhoes(data.pavilhoes);
                    zoomPointsAnimate();
                } else {
                    if (!msg_error) {
                        Swal.fire({
                            type: 'error',
                            title: 'Ocorreu um erro de comunicação. Por favor tente mais tarde.',
                        })
                        msg_error = true;
                    }
                }
            },
            error: function () {
                if (!msg_error) {
                    Swal.fire({
                        type: 'error',
                        title: 'Ocorreu um erro de comunicação. Por favor tente mais tarde.',
                    })
                    msg_error = true;
                }
            }
        });
    }

    // chamada ajax para filtrar os dados dos clubes/Universidade
    function filterRep(json) {
        $.ajax({
            url: 'php/filterRep.php',
            type: 'POST',
            data: {
                myData: JSON.stringify(json),
            },
            dataType: 'json',
            cache: false,
            success: function (data) {
                if (data.error === 'none') {
                    clearLayers();
                    if (data.municipio) addPolygon(data.municipio);
                    else if (data.freguesia) addPolygon(data.freguesia);
                    else onlyPoints = true;
                    addRepresentacoes(data.rep);
                    zoomPointsAnimate();
                } else {
                    if (!msg_error) {
                        Swal.fire({
                            type: 'error',
                            title: 'Ocorreu um erro de comunicação. Por favor tente mais tarde.',
                        })
                        msg_error = true;
                    }
                }
            },
            error: function () {
                if (!msg_error) {
                    Swal.fire({
                        type: 'error',
                        title: 'Ocorreu um erro de comunicação. Por favor tente mais tarde.',
                    })
                    msg_error = true;
                }
            }
        });
    }

    function resetRouting() {
        $("#visto").remove();
        $("#pontoPartida").after("<input id='inputPontoInicial' value=' selecione no mapa'></input>");
        $("#visto2").remove();
        $("#pontoChegada").after("<input id='inputPontoFinal' value=' selecione no mapa'></input>");
        $("#distancia").remove();
        $("#tempoChegada").remove();
    }

    // quando clica numa option de um select das infraestruturas
    $(document).on('change', ".select_pav", function () {
        map.removeOverlay(popup);
        map.removeInteraction(select);
        routing = false;
        resetRouting();
        var mun, freg, mod, coberto, preco, json = {};
        mun = $('#municipio_pav').val();
        freg = $('#freguesia_pav').val();
        mod = $('#modalidade_pav').val();
        coberto = $('#coberto_pav').val();
        preco = $('#preco_pav').val();
        if (this.id === 'municipio_pav') {
            if (mun !== 'Todos') {
                optionsFreguesias(mun, 'freguesia_pav');
                json['municipio'] = mun;
                freg = $('#freguesia_pav').val();
            } else {
                $('#freguesia_pav').html('<option>Todas</option>');
                $('#freguesia_pav').prop('disabled', true);
            }
        } else {
            if (mun !== 'Todos' && freg === 'Todas') json['municipio'] = mun;
            else {
                if (freg !== 'Todas' && mun !== 'Todos') json['freguesia'] = freg;
            }
        }
        if (mod !== 'Todas') json['modalidade'] = mod;
        if (coberto !== 'Ambos') json['coberto'] = coberto;
        if (preco !== 'Todos') json['preco'] = preco;

        filterPav(json);
    });

    // quando clica numa option de um select dos clubes
    $(document).on('change', ".select_clubes", function () {
        map.removeOverlay(popup);
        map.removeInteraction(select);
        routing = false;
        resetRouting();
        var mun, freg, mod, escalao, genero, adaptado, json = {};
        mun = $('#municipio_clubes').val();
        freg = $('#freguesia_clubes').val();
        mod = $('#modalidade_clubes').val();
        escalao = $('#escalao_clubes').val();
        genero = $('#genero_clubes').val();
        adaptado = $('#adaptado_clubes').val();
        if (this.id === 'municipio_clubes') {
            if (mun !== 'Todos') {
                optionsFreguesias(mun, 'freguesia_clubes');
                json['municipio'] = mun;
                freg = $('#freguesia_clubes').val();
            } else {
                $('#freguesia_clubes').html('<option>Todas</option>');
                $('#freguesia_clubes').prop('disabled', true);
            }
        } else {
            if (mun !== 'Todos' && freg === 'Todas') json['municipio'] = mun;
            else if (freg !== 'Todas') json['freguesia'] = freg;
            else if (mun === 'Todos') {
                if ($('#freguesia_clubes').is(':enabled')) {
                    $('#freguesia_clubes').html('<option>Todas</option>');
                    $('#freguesia_clubes').prop('disabled', true);
                }
            }
            if (this.id === 'modalidade_clubes') {
                if (mod !== 'Todas') optionsEscaloes(mod);
                else {
                    $('#escalao_clubes').html("<option>Todos</option>");
                    $('#escalao_clubes').prop('disabled', true);
                }
                mod = $('#modalidade_clubes').val();
                escalao = $('#escalao_clubes').val();
            }
        }
        if (escalao !== 'Todos' && escalao !== 'Ambos') {
            json['modalidade'] = mod;
            json['escalao'] = escalao;
        } else {
            if (mod !== 'Todas') json['modalidade'] = mod;
        }
        if (genero !== 'Ambos') json['genero'] = genero;
        if (adaptado !== 'Ambos') json['adaptado'] = adaptado;

        filterRep(json);
    });

    var escolhaPavRes = 1;
    var modalidadesFinais2;

    //dropdown da pesquisa manual
    $('input.typeahead').typeahead({
        source: function (query, process) {
            if (escolhaPavRes == 1) {
                return $.get('php/dropdownPav.php', {
                    query: query
                }, function (data) {
                    data = $.parseJSON(data);
                    return process(data);
                });
            } else {
                return $.get('php/dropdownClubes.php', {
                    query: query
                }, function (data) {
                    data = $.parseJSON(data);
                    return process(data);
                });
            }
        }
    });

    // pesquisa array de todas as modalidades do pavilhao selecionado no mapa
    function pesqModalidadee(nomeM) {
        $.ajax({
            url: "php/pesqModalidades.php",
            type: 'post',
            data: {
                'nomeM': nomeM
            },
            dataType: 'json',
            success: function (data) {
                if (data.erro === "1")
                    return "Ocorreu erro";
                else {
                    modalidadesFinais2 = "";
                    for (x in data.pesquisa) {
                        modalidadesFinais2 += data.pesquisa[x].modalidade + " ,";
                    }
                }
            },
            error: function () {
                return "Ocorreu erro de comunicação"
            }
        });

    }

    // mostra os detalhes do pavilhao selecionado no mapa
    function legendarr(nomeP) {
        $('#conteudo').html(loading_spinner);
        $.ajax({
            url: "php/legenda.php",
            type: 'post',
            data: {
                'nomeP': nomeP
            },
            dataType: 'json',
            success: function (data) {
                if (data.erro === "1") {
                    if (!msg_error) {
                        Swal.fire({
                            type: 'error',
                            title: 'Ocorreu um erro de comunicação. Por favor tente mais tarde.',
                        })
                        msg_error = true;
                    }
                    $('#conteudo').html(error_msg);
                } else {
                    var novo = "";
                    var pavilhaoM = data.nomeE[0];
                    pesqModalidadee(pavilhaoM);
                    for (x in data.nomeE) {
                        var imagemm = data.imagemE[x];
                        if (imagemm === null) {
                            imagemm = "img/20170817_123101tin.jpg";
                        }
                        novo += "<button type='button' class='close fecharLeg' aria-label='Close'><span aria-hidden='true'>&times;</span></button><div class='list-group point-list-view'><div class='imagP'><img src='" + imagemm + "' width='220' height='83'></div><table class='table table-borderless table-sm'><tbody><tr><th>Nome:</th><td>" + data.nomeE[x] + "</td></tr><tr><th>Tipo:</th><td>" + data.tipologiaE[x] + "</td></tr><tr><th>Morada:</th><td>" + data.moradaE[x] + "</td></tr><tr><th>Modalidades:</th><td>" + modalidadesFinais2 + "</td></tr><tr><th>Codigo-Postal:</th><td>" + data.codPostalE[x] + "</td></tr><tr><th>Freguesia:</th><td>" + data.freguesiaE[x] + "</td></tr><tr><th>Contacto:</th><td>" + data.contactoE[x] + "</td></tr><tr><th>Preço:</th><td>" + data.precoE[x] + " €</td></tr><tr><th>Hora Abertura:</th><td>" + data.hAberturaE[x] + " h</td></tr><tr><th>Hora Fecho:</th><td>" + data.hFechoE[x] + " h</td></tr></div>";
                    }
                    $('#conteudo').html(novo);
                    $('.fecharLeg').click(function () {
                        var local = $("#inputPesquisar").val();
                        pesquisar(local);
                    })
                }
            },
            error: function () {
                if (!msg_error) {
                    Swal.fire({
                        type: 'error',
                        title: 'Ocorreu um erro de comunicação. Por favor tente mais tarde.',
                    })
                    msg_error = true;
                }
            }
        });
    }

    var escaloess;
    var generoo;
    var adaptadoo;
    var modalidadE;

    // obtem os valores dos arrays de escaloes, genero, adaptado e modalidade do Clube/Universidade selecionado
    function pesqEscalao(nomeM) {
        $.ajax({
            url: "php/clubedetalhes.php",
            async: false,
            type: 'post',
            data: {
                'nomeM': nomeM
            },
            dataType: 'json',
            success: function (data) {
                if (data.erro === "1")
                    return "Ocorreu erro";
                else {
                    escaloess = "";
                    generoo = "";
                    adaptadoo = "";
                    modalidadE = "";
                    for (x in data.pesquisa) {
                        escaloess += data.pesquisa[x].escalao + " ,";
                        if (generoo.indexOf(data.pesquisa[x].genero) == -1) {
                            generoo += data.pesquisa[x].genero + " ,";
                        }
                        if (adaptadoo.indexOf(data.pesquisa[x].adaptado) == -1) {
                            adaptadoo += data.pesquisa[x].adaptado + " ,";
                        }
                        if (modalidadE.indexOf(data.pesquisa[x].modalidade) == -1) {
                            modalidadE += data.pesquisa[x].modalidade + " ,";
                        }
                    }
                }
            },
            error: function () {
                return "Ocorreu erro de comunicação"
            }
        });
    }

    // mostra detalhes do Clube/Universidade Selecionado
    function legendarM(nomeP) {
        $('#conteudo').html(loading_spinner);
        $.ajax({
            url: "php/municipiolocais.php",
            type: 'post',
            data: {
                'nomeP': nomeP
            },
            dataType: 'json',
            success: function (data) {
                if (data.erro === "1") {
                    if (!msg_error) {
                        Swal.fire({
                            type: 'error',
                            title: 'Ocorreu um erro de comunicação. Por favor tente mais tarde.',
                        })
                        msg_error = true;
                    }
                    $('#conteudo').html(error_msg);
                } else {
                    var novo = "";
                    var clubeM = data.nomeE[0];
                    pesqEscalao(clubeM);
                    zoomD(clubeM);
                    for (x in data.nomeE) {
                        novo += "<button type='button' class='close fecharLeg' aria-label='Close'><span aria-hidden='true'>&times;</span></button><div class='list-group point-list-view'><div class='imagM'><img src='img/cms-image-000013270.jpg' width='200' height='80'></div><table class='table table-borderless table-sm'><tbody><tr><th>Nome:</th><td>" + data.nomeE[x] + "</td></tr><tr><th>Tipo:</th><td>" + data.tipologiaE[x] + "</td></tr><tr><th>Municipio:</th><td>" + data.municipioE[x] + "</td></tr><tr><th>Escalôes:</th><td>" + escaloess + "</td></tr><tr><th>Género:</th><td>" + generoo + "</td></tr><tr><th>Adaptado:</th><td>" + adaptadoo + "</td></tr><tr><th>Modalidade:</th><td>" + modalidadE + "</td></tr></div>";
                    }
                    $('#conteudo').html(novo);
                    $('.fecharLeg').click(function () {
                        var local = $("#inputPesquisar").val();
                        pesquisarPClubes(local);
                    })
                }
            },
            error: function () {
                if (!msg_error) {
                    Swal.fire({
                        type: 'error',
                        title: 'Ocorreu um erro de comunicação. Por favor tente mais tarde.',
                    })
                    msg_error = true;
                }
            }
        });
    }

    var modalidadesFinais;

    //carregar no enter na pesquisa manual INICIO
    $("#FormPesq").on('submit', function (e) {
        e.preventDefault();
    });
    var local = $("#inputPesquisar").val();
    pesquisar(local);
    $('#inputPesquisar').bind("enterKey", function (e) {
        var local = $("#inputPesquisar").val();
        if (escolhaPavRes == 1) {
            pesquisar(local);
        } else {
            pesquisarPClubes(local);
        }
    });
    $('#inputPesquisar').keyup(function (e) {
        if (e.keyCode == 13) {
            $(this).trigger("enterKey");
        }
    });

    //carregar no enter na pesquisa manual FIM
    //pesquisa array de todas as modalidades do pavilhao selecionado nos resultados dos detalhes 
    function pesqModalidade(nomeM) {
        $.ajax({
            url: "php/pesqModalidades.php",
            type: 'post',
            data: {
                'nomeM': nomeM
            },
            dataType: 'json',
            success: function (data) {
                if (data.erro === "1")
                    return "Ocorreu erro";
                else {
                    modalidadesFinais = "";
                    for (x in data.pesquisa) {
                        modalidadesFinais += data.pesquisa[x].modalidade + " ,";
                    }
                }
            },
            error: function () {
                return "Ocorreu erro de comunicação"
            }
        });
    }

    // faz zoom para o ponto com o nome selecionado
    function zoomD(pav) {
        gjson_layer.getSource().forEachFeature(function (feature) {
            var att = feature.get('nome');
            if (att == pav) {
                map.getView().fit(feature.getGeometry().getExtent(), {
                    duration: 500,
                    maxZoom: 16,
                    padding: [0, 0, 0, 600]
                });
            }
        })
    }

    // mostra detalhes do pavilhao selecionado dos resultados em detalhes
    function legendar(nomeP) {
        $('#conteudo').html(loading_spinner);
        $.ajax({
            url: "php/legenda.php",
            type: 'post',
            data: {
                'nomeP': nomeP
            },
            dataType: 'json',
            success: function (data) {
                if (data.erro === "1") {
                    if (!msg_error) {
                        Swal.fire({
                            type: 'error',
                            title: 'Ocorreu um erro de comunicação. Por favor tente mais tarde.',
                        })
                        msg_error = true;
                    }
                    $('#conteudo').html(error_msg);
                } else {
                    var novo = "";
                    var pavilhaoM = data.nomeE[0];
                    pesqModalidade(pavilhaoM);
                    zoomD(pavilhaoM);
                    for (x in data.nomeE) {
                        var imagemm = data.imagemE[x];
                        if (imagemm === null) {
                            imagemm = "img/20170817_123101tin.jpg";
                        }
                        novo += "<button type='button' class='close fecharLeg' aria-label='Close'><span aria-hidden='true'>&times;</span></button><div class='list-group point-list-view'><div class='imagP'><img src='" + imagemm + "' width='220' height='83'></div><table class='table table-borderless table-sm'><tbody><tr><th>Nome:</th><td>" + data.nomeE[x] + "</td></tr><tr><th>Tipo:</th><td>" + data.tipologiaE[x] + "</td></tr><tr><th>Morada:</th><td>" + data.moradaE[x] + "</td></tr><tr><th>Modalidades:</th><td>" + modalidadesFinais + "</td></tr><tr><th>Codigo-Postal:</th><td>" + data.codPostalE[x] + "</td></tr><tr><th>Freguesia:</th><td>" + data.freguesiaE[x] + "</td></tr><tr><th>Contacto:</th><td>" + data.contactoE[x] + "</td></tr><tr><th>Preço:</th><td>" + data.precoE[x] + " €</td></tr><tr><th>Hora Abertura:</th><td>" + data.hAberturaE[x] + " h</td></tr><tr><th>Hora Fecho:</th><td>" + data.hFechoE[x] + " h</td></tr></div>";
                    }
                    $('#conteudo').html(novo);
                    $('.fecharLeg').click(function () {
                        var local = $("#inputPesquisar").val();
                        pesquisar(local);
                    })
                }
            },
            error: function () {
                if (!msg_error) {
                    Swal.fire({
                        type: 'error',
                        title: 'Ocorreu um erro de comunicação. Por favor tente mais tarde.',
                    })
                    msg_error = true;
                }
                $('#conteudo').html(error_msg);
            }
        });
    }

    // mostra os resultados em Detalhes dos pavilhoes pesquisados manualmente
    function pesquisar(local) {
        $('#conteudo').html(loading_spinner);
        $.ajax({
            url: "php/pesquisa.php",
            type: 'post',
            data: {
                'local': local
            },
            dataType: 'json',
            success: function (data) {
                if (data.erro === "1") {
                    if (!msg_error) {
                        Swal.fire({
                            type: 'error',
                            title: 'Ocorreu um erro de comunicação. Por favor tente mais tarde.',
                        })
                        msg_error = true;
                    }
                    $('#conteudo').html(error_msg);
                } else {
                    var txt = "";
                    for (x in data.pesquisa) {
                        var imagemm = data.pesquisa[x].imagem;
                        if (imagemm === null) {
                            imagemm = "img/20170817_123101tin.jpg";
                        }
                        txt += "<div class='resultados'><div class='divDireita'><img src='" + imagemm + "' style='width:42px;height:42px;'></div>" +
                            "<div class='divEsquerda'><h5 class='list-group-item-heading pavilhoes'>" + data.pesquisa[x].nome + "</h5>" +
                            "<p class='list-group-item-text'>" + data.pesquisa[x].morada + "</p></div></div>";
                    }
                    $('#conteudo').html(txt);
                    $(".resultados").click(function () {
                        var nomeP = $(this).find('.pavilhoes').html();
                        legendar(nomeP);
                    });
                }
            },
            error: function () {
                if (!msg_error) {
                    Swal.fire({
                        type: 'error',
                        title: 'Ocorreu um erro de comunicação. Por favor tente mais tarde.',
                    })
                    msg_error = true;
                }
                $('#conteudo').html(error_msg);
            }
        });
    }

    // clicar no botao Pesquisar
    $("#btnPesquisar").click(function () {
        routing = false;
        clearRouting();
        var local = $("#inputPesquisar").val();
        if (escolhaPavRes == 1) {
            pesquisar(local);
        } else {
            pesquisarPClubes(local);
        }
    });

    // mostra os resultados em Detalhes dos clubes pesquisados manualmente
    function pesquisarPClubes(local) {
        $('#conteudo').html(loading_spinner);
        $.ajax({
            url: "php/pesquisaClubes.php",
            type: 'post',
            data: {
                'local': local
            },
            dataType: 'json',
            success: function (data) {
                if (data.erro === "1") {
                    if (!msg_error) {
                        Swal.fire({
                            type: 'error',
                            title: 'Ocorreu um erro de comunicação. Por favor tente mais tarde.',
                        })
                        msg_error = true;
                    }
                    $('#conteudo').html(error_msg);
                } else {
                    var txt = "";
                    for (x in data.pesquisa)
                        txt += "<div class=\"list-group point-list-view resultados\">" +
                        "<h5 class='list-group-item-heading pavilhoes'>" + data.pesquisa[x].nome + "</h5>" +
                        "<p class=\"list-group-item-text\">" + data.pesquisa[x].tipo + "</p>" +
                        "</div>";
                    $('#conteudo').html(txt);
                    $(".resultados").click(function () {
                        var nomeP = $(this).find('.pavilhoes').html();
                        legendarM(nomeP);
                    });
                }
            },
            error: function () {
                if (!msg_error) {
                    Swal.fire({
                        type: 'error',
                        title: 'Ocorreu um erro de comunicação. Por favor tente mais tarde.',
                    })
                    msg_error = true;
                }
                $('#conteudo').html(error_msg);
            }
        });
    }

    // passa os detalhes para clubes
    $("#linkclubes").click(function () {
        escolhaPavRes = 2;
        var local = "";
        pesquisarPClubes(local);
        if (points_routing) {
            // Fazer reset ás entidades "ponto inicial" e "ponto destino".
            pontoInicial.setGeometry(null);
            pontoDestino.setGeometry(null);
            // Remover layer "resultado".
            source_route.clear();
        }
        routing = false;
    });

    // passa os detalhes para os pavilhoes
    $("#linkpav").click(function () {
        escolhaPavRes = 1;
        var local = "";
        pesquisar(local);
        if (points_routing) {
            // Fazer reset ás entidades "ponto inicial" e "ponto destino".
            pontoInicial.setGeometry(null);
            pontoDestino.setGeometry(null);
            // Remover layer "resultado".
            source_route.clear();
        }
        routing = false;
    });

    var meioT = 0;

    // MapBox API
    var token = "access_token=pk.eyJ1IjoicGVkcm9mZXJyZWlyYTk3IiwiYSI6ImNqd2kxOTk3ZTAycjE0M281M2FjdG9qdm0ifQ.Ew9-RmuscnK-jQmR_-Gl5Q";
    var baseUrl = "https://api.mapbox.com/directions/v5/mapbox/";
    var profile = ["walking", "cycling", "driving"];
    var geometries = "&geometries=geojson";

    function styleLine() {
        var p = new ol.style.Style({
            stroke: new ol.style.Stroke({
                color: 'green',
                width: 6
            })
        });
        return p;
    }

    $(document).on('click', '#walking', function () {
        meioT = 0;
        $("#walking").css('background-color', 'grey');
        $("#cycling").css('background-color', '#f0f0f0');
        $("#driving").css('background-color', '#f0f0f0');
    })
    $(document).on('click', '#cycling', function () {
        $("#walking").css('background-color', '#f0f0f0');
        $("#cycling").css('background-color', 'grey');
        $("#driving").css('background-color', '#f0f0f0');
        meioT = 1;
    })
    $(document).on('click', '#driving', function () {
        $("#walking").css('background-color', '#f0f0f0');
        $("#cycling").css('background-color', '#f0f0f0');
        $("#driving").css('background-color', 'grey');
        meioT = 2;
    })

    $("#directions").click(function () {
        clearRouting();
        routing = true;
        $("#testeDir").text("Direções");
        $("#conteudo").html(
            "<div id='meioTransporte'>" +
            "<div class='text-center'>" +
            "<button id='walking' class='meioTransporteButton'><i " +
            "class='material-icons'>directions_walk</i></button>" +
            "<button id='cycling' class='meioTransporteButton'><i " +
            "class='material-icons'>directions_bike</i></button>" +
            "<button id='driving' class='meioTransporteButton'><i " +
            "class='material-icons'>directions_car</i></button>" +
            "</div><br>" +
            "<div>" +
            "<span id='pontoPartida'>Ponto de Partida: &nbsp</span>" +
            "<input id='inputPontoInicial' value='Selecione no mapa' disabled></input>" +
            "</div>" +
            "<div>" +
            "<span id='pontoChegada'>Ponto de Chegada: &nbsp</span>" +
            "<input id='inputPontoFinal' value='Selecione no mapa' disabled></input><br>" +
            "</div>" +
            "<button id='btnLimpar' class='btn btn-primary'>Limpar</button>" +
            "</div>");

        // As features "ponto de partida" e "ponto de destino".
        pontoInicial = new ol.Feature();
        pontoDestino = new ol.Feature();
        // O layer vetorial utilizado para apresentar
        //as entidades ponto de partida e ponto de chegada .
        points_routing = new ol.layer.Vector({
            source: new ol.source.Vector({
                features: [pontoInicial, pontoDestino]
            }),
        });
        map.addLayer(points_routing);

    });

    // Função de transformação para converter coordenadas de 
    // EPSG:3857 para EPSG:4326.
    var transform = ol.proj.getTransform('EPSG:3857', 'EPSG:4326');
    // Registar um listener "click" no mapa.
    map.on('click', function (event) {
        if (routing === true) {
            if (pontoInicial.getGeometry() == null) {
                // Primeiro click.
                pontoInicial.setGeometry(
                    new ol.geom.Point(event.coordinate)
                );
                $("#inputPontoInicial").remove();
                $("#pontoPartida").after('<i id="visto" class="material-icons">done</i>');
            } else if (pontoDestino.getGeometry() == null) {
                //Segundo click.
                pontoDestino.setGeometry(new ol.geom.Point(event.coordinate));
                $("#inputPontoFinal").remove();
                $("#pontoChegada").after('<i id="visto2" class="material-icons">done</i>');
                // Transformar as coordenadas da projeção do mapa (EPSG:3857)
                // para a projeção dos dados na base de dados (EPSG:4326).
                var coordInicial = transform(
                    pontoInicial.getGeometry().getCoordinates());
                var coordDestino = transform(pontoDestino.getGeometry().getCoordinates());
                //rota
                $.ajax({
                    url: baseUrl + profile[meioT] + "/" + coordInicial[0] + "%2C" + coordInicial[1] + "%3B" + coordDestino[0] + "%2C" + coordDestino[1] + ".json?" + token + geometries,
                    type: 'get',
                    success: function (data) {
                        duration = JSON.stringify(data.routes[0].duration);
                        var minutes = Math.floor(duration / 60);
                        var seconds = duration - minutes * 60;
                        seconds = seconds.toFixed(2);
                        distance = JSON.stringify(data.routes[0].distance);
                        data = JSON.stringify(data.routes[0].geometry);
                        $("#visto2").after('<div id="tempoChegada"><br>Tempo de chegada: ' + minutes + ' minutos e ' + seconds + ' segundos.</div>');
                        $("#visto2").after('<div id="distancia"><br>Distância: ' + distance + ' metros</div>');
                        var gjson = new ol.format.GeoJSON({
                            featureProjection: "EPSG:3857"
                        });
                        source_route = new ol.source.Vector({
                            features: gjson.readFeatures(data)
                        });
                        gjson_layer = new ol.layer.Vector({
                            source: source_route,
                            style: styleLine()
                        });
                        map.addLayer(gjson_layer);
                    },
                    error: function () {
                        if (!msg_error) {
                            Swal.fire({
                                type: 'error',
                                title: 'Ocorreu um erro de comunicação. Por favor tente mais tarde.',
                            })
                            msg_error = true;
                        }
                    }
                });
            }
        }
    });

    $(document).on('click', '#btnLimpar', function () {
        if (points_routing) {
            // Fazer reset ás entidades "ponto inicial" e "ponto destino".
            pontoInicial.setGeometry(null);
            pontoDestino.setGeometry(null);
            // Remover layer "resultado".
            source_route.clear();
            $("#visto").remove();
            $("#pontoPartida").after("<input id='inputPontoInicial' value=' selecione no mapa'></input>");
            $("#visto2").remove();
            $("#pontoChegada").after("<input id='inputPontoFinal' value=' selecione no mapa'></input>");
            $("#distancia").remove();
            $("#tempoChegada").remove();
        }
    });
});