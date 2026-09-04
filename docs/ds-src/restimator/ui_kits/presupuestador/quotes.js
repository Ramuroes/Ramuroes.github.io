/* Shared mock estimate dataset for the Presupuestador RE kit.
   Keyed by saved-quote id (GV-A-…). Consumed by ClientSummary (S3) and
   Calculator (S1) so the reopen loop loads the real estimate per id.
   This is mock data only — no calculation engine lives here. */
(function () {
  // calc.term matches Calculator <select id="termSel"> values; calc.coloc matches colocSel; calc.zona matches zonaSel.
  window.RE_QUOTES = {
    'GV-A-00148': {
      num: 'PRE-2026-0148', status: 'draft', date: '16/06/2026',
      client: 'Mariana Soto', ctype: 'Particular', contact: '099 412 387', obra: 'Parque del Plata, Canelones', atendido: 'Ramiro Estavillo',
      fam: 'Escaleras', sub: 'Recta',
      title: 'Escalera recta de un tramo — hierro',
      desc: 'Estructura en hierro con terminación DTM (pintura al horno), baranda de un lado en caño y planchuela, y contrahuella en chapa plegada. Colocación incluida.',
      dims: [['Ancho de paso', '1,00 m'], ['Altura total', '2,90 m'], ['Avance', '3,55 m'], ['Descanso', 'No']],
      items: [['Fabricación — escalera recta', 'estructura en hierro, terminación DTM', 169000], ['Baranda lado izquierdo', 'caño y planchuela, misma terminación', 14800], ['Contrahuella', 'chapa plegada en todos los escalones', 13500], ['Colocación — incluida (simple)', 'montaje y anclajes en obra', 19700], ['Traslado — zona Canelones', 'viático por distancia de obra', 2500]],
      sub: 219500, total: 267800,
      calc: { ancho: '1,00', alt: '2,90', av: '3,55', term: '1.00', coloc: '0.10', zona: 'Canelones', tipo: 'Particular', baranda: true, contra: true, piso: false, led: false, ovr: '' }
    },
    'GV-A-00147': {
      num: 'PRE-2026-0147', status: 'sent', date: '15/06/2026',
      client: 'Estudio Caviglia', ctype: 'Arquitecto', contact: '2901 7744', obra: 'Edificio Rambla, Pocitos', atendido: 'Ramiro Estavillo',
      fam: 'Barandas', sub: 'Balcón',
      title: 'Baranda de balcón — caño y planchuela',
      desc: 'Baranda de balcón en caño y planchuela con terminación galvanizada, anclajes a losa y pasamanos continuo.',
      dims: [['Largo', '4,20 m'], ['Altura', '1,05 m'], ['Módulos', '3'], ['Lado', 'Exterior']],
      items: [['Fabricación — baranda de balcón', 'caño y planchuela, galvanizada', 78300], ['Anclajes y planchuela de fijación', 'a losa de hormigón', 8400], ['Colocación — incluida (simple)', 'montaje y nivelación', 5400]],
      sub: 92100, total: 112400,
      calc: { ancho: '0,90', alt: '1,05', av: '4,20', term: '1.15', coloc: '0.10', zona: 'Montevideo', tipo: 'Arquitecto / Estudio', baranda: false, contra: false, piso: false, led: false, ovr: '' }
    },
    'GV-A-00146': {
      num: 'PRE-2026-0146', status: 'appr', date: '14/06/2026',
      client: 'Metalúrgica Sur', ctype: 'Empresa', contact: '2200 3380', obra: 'Planta industrial, Ruta 1', atendido: 'Ramiro Estavillo',
      fam: 'Portones', sub: 'Corredizo',
      title: 'Portón corredizo de acceso vehicular',
      desc: 'Portón corredizo en marco de caño estructural con relleno de chapa, riel superior con herrajes reforzados. Terminación DTM. Colocación compleja.',
      dims: [['Ancho', '4,50 m'], ['Altura', '2,20 m'], ['Hojas', '1'], ['Riel', 'Superior']],
      items: [['Fabricación — portón corredizo', 'marco estructural + chapa, DTM', 348000], ['Riel superior y herrajes', 'rodamientos reforzados', 31600], ['Colocación — incluida (compleja)', 'montaje, guías y nivelación', 16000], ['Traslado — Ruta 1', 'viático por distancia de obra', 6000]],
      sub: 401600, total: 489900,
      calc: { ancho: '4,50', alt: '2,20', av: '4,50', term: '1.00', coloc: '0.15', zona: 'Interior', tipo: 'Empresa', baranda: false, contra: false, piso: false, led: false, ovr: '' }
    },
    'GV-A-00145': {
      num: 'PRE-2026-0145', status: 'rej', date: '13/06/2026',
      client: 'Carlos Gómez', ctype: 'Particular', contact: '098 220 145', obra: 'Casa, Lagomar', atendido: 'Ramiro Estavillo',
      fam: 'Cerramientos', sub: 'Patio',
      title: 'Cerramiento de patio — paño fijo',
      desc: 'Cerramiento de patio en perfilería de hierro con paño fijo y puerta de acceso peatonal. Terminación DTM.',
      dims: [['Ancho', '3,20 m'], ['Altura', '2,00 m'], ['Superficie', '6,4 m²'], ['Puerta', 'Sí']],
      items: [['Fabricación — cerramiento de patio', 'perfilería de hierro, paño fijo', 55300], ['Puerta de acceso peatonal', 'incluida en el paño', 5400], ['Colocación — incluida (simple)', 'fijación a muros', 3400]],
      sub: 64100, total: 78200,
      calc: { ancho: '3,20', alt: '2,00', av: '3,20', term: '1.00', coloc: '0.10', zona: 'Canelones', tipo: 'Particular', baranda: false, contra: false, piso: false, led: false, ovr: '' }
    },
    'GV-A-00144': {
      num: 'PRE-2026-0144', status: 'sent', date: '12/06/2026',
      client: 'Mariana Soto', ctype: 'Particular', contact: '099 412 387', obra: 'Dúplex, Carrasco', atendido: 'Ramiro Estavillo',
      fam: 'Escaleras', sub: 'Caracol',
      title: 'Escalera caracol — dúplex',
      desc: 'Escalera caracol en hierro con eje central, escalones en chapa plegada y baranda perimetral. Terminación DTM. Colocación incluida.',
      dims: [['Diámetro', '1,60 m'], ['Altura total', '2,80 m'], ['Vueltas', '1¼'], ['Descanso', 'No']],
      items: [['Fabricación — escalera caracol', 'eje central + escalones en chapa', 214000], ['Baranda perimetral', 'caño y planchuela', 34000], ['Colocación — incluida (simple)', 'montaje y anclaje de eje', 14000], ['Traslado — Carrasco', 'viático por distancia de obra', 4000]],
      sub: 266000, total: 324500,
      calc: { ancho: '1,60', alt: '2,80', av: '1,60', term: '1.00', coloc: '0.10', zona: 'Montevideo', tipo: 'Particular', baranda: true, contra: true, piso: false, led: false, ovr: '' }
    },
    'GV-A-00143': {
      num: 'PRE-2026-0143', status: 'appr', date: '11/06/2026',
      client: 'Ferretería Central', ctype: 'Empresa', contact: '2924 1190', obra: 'Local comercial, Centro', atendido: 'Ramiro Estavillo',
      fam: 'Puertas', sub: 'Doble hoja',
      title: 'Puerta doble hoja — taller',
      desc: 'Puerta de doble hoja en marco reforzado con relleno de chapa, cerradura reforzada y burlete perimetral. Terminación DTM.',
      dims: [['Ancho', '2,00 m'], ['Altura', '2,10 m'], ['Hojas', '2'], ['Marco', 'Reforzado']],
      items: [['Fabricación — puerta doble hoja', 'marco reforzado + chapa, DTM', 138000], ['Cerradura reforzada', 'con tres puntos de anclaje', 12300], ['Colocación — incluida (simple)', 'montaje y ajuste de marco', 12000]],
      sub: 162300, total: 198000,
      calc: { ancho: '2,00', alt: '2,10', av: '2,00', term: '1.00', coloc: '0.10', zona: 'Montevideo', tipo: 'Empresa', baranda: false, contra: false, piso: false, led: false, ovr: '' }
    },
    'GV-A-00142': {
      num: 'PRE-2026-0142', status: 'draft', date: '10/06/2026',
      client: 'Estudio Caviglia', ctype: 'Arquitecto', contact: '2901 7744', obra: 'Vivienda, Punta Gorda', atendido: 'Ramiro Estavillo',
      fam: 'Barandas', sub: 'Recta caño',
      title: 'Baranda escalera de servicio — caño',
      desc: 'Baranda recta para escalera de servicio en caño, pasamanos continuo y montantes verticales. Terminación DTM.',
      dims: [['Largo', '3,10 m'], ['Altura', '0,95 m'], ['Tramos', '1'], ['Lado', 'Interior']],
      items: [['Fabricación — baranda de caño', 'montantes y pasamanos continuo', 44600], ['Colocación — incluida (simple)', 'fijación a escalón y muro', 8400]],
      sub: 53000, total: 64700,
      calc: { ancho: '0,90', alt: '0,95', av: '3,10', term: '1.00', coloc: '0.10', zona: 'Montevideo', tipo: 'Arquitecto / Estudio', baranda: false, contra: false, piso: false, led: false, ovr: '' }
    },
    'GV-A-00141': {
      num: 'PRE-2026-0141', status: 'cancel', date: '09/06/2026',
      client: 'Carlos Gómez', ctype: 'Particular', contact: '098 220 145', obra: 'Quinta, Pando', atendido: 'Ramiro Estavillo',
      fam: 'Portones', sub: 'Batiente',
      title: 'Portón batiente — quinta',
      desc: 'Portón batiente de dos hojas en marco de caño estructural, bisagras reforzadas y pasador central. Terminación galvanizada.',
      dims: [['Ancho', '3,60 m'], ['Altura', '2,10 m'], ['Hojas', '2'], ['Apertura', 'Hacia adentro']],
      items: [['Fabricación — portón batiente', 'dos hojas, marco estructural', 176000], ['Bisagras reforzadas y pasador', 'herrajes de servicio pesado', 17300], ['Colocación — incluida (compleja)', 'montaje de columnas y hojas', 16000]],
      sub: 209300, total: 255300,
      calc: { ancho: '3,60', alt: '2,10', av: '3,60', term: '1.15', coloc: '0.15', zona: 'Canelones', tipo: 'Particular', baranda: false, contra: false, piso: false, led: false, ovr: '' }
    },
    'GV-A-00140': {
      num: 'PRE-2026-0140', status: 'appr', date: '06/06/2026',
      client: 'Metalúrgica Sur', ctype: 'Empresa', contact: '2200 3380', obra: 'Oficinas, Ruta 1', atendido: 'Ramiro Estavillo',
      fam: 'Escaleras', sub: 'U / con descanso',
      title: 'Escalera en U con descanso',
      desc: 'Escalera en U de dos tramos con descanso intermedio, estructura en hierro, baranda en ambos tramos y contrahuella en chapa. Terminación DTM.',
      dims: [['Ancho de paso', '1,10 m'], ['Altura total', '3,10 m'], ['Avance', '2,40 m'], ['Descanso', 'Sí']],
      items: [['Fabricación — escalera en U', 'dos tramos + descanso intermedio', 248000], ['Baranda dos tramos', 'caño y planchuela', 38000], ['Contrahuella', 'chapa plegada', 9800], ['Colocación — incluida (simple)', 'montaje y anclajes', 14000]],
      sub: 309800, total: 378000,
      calc: { ancho: '1,10', alt: '3,10', av: '2,40', term: '1.00', coloc: '0.10', zona: 'Interior', tipo: 'Empresa', baranda: true, contra: true, piso: false, led: false, ovr: '' }
    },
    'GV-A-00139': {
      num: 'PRE-2026-0139', status: 'sent', date: '05/06/2026',
      client: 'Ferretería Central', ctype: 'Empresa', contact: '2924 1190', obra: 'Depósito, Sayago', atendido: 'Ramiro Estavillo',
      fam: 'Cerramientos', sub: 'Patio',
      title: 'Cerramiento de depósito',
      desc: 'Cerramiento de depósito en perfilería de hierro con paño fijo de chapa y puerta de acceso. Terminación galvanizada.',
      dims: [['Ancho', '4,10 m'], ['Altura', '2,40 m'], ['Superficie', '9,8 m²'], ['Puerta', 'Sí']],
      items: [['Fabricación — cerramiento depósito', 'perfilería + paño de chapa', 101300], ['Puerta de acceso', 'con cerradura simple', 9400], ['Colocación — incluida (simple)', 'fijación a estructura existente', 7000]],
      sub: 117700, total: 143600,
      calc: { ancho: '4,10', alt: '2,40', av: '4,10', term: '1.15', coloc: '0.10', zona: 'Montevideo', tipo: 'Empresa', baranda: false, contra: false, piso: false, led: false, ovr: '' }
    },
    'GV-A-00138': {
      num: 'PRE-2026-0138', status: 'draft', date: '04/06/2026',
      client: 'Mariana Soto', ctype: 'Particular', contact: '099 412 387', obra: 'Oficina, Cordón', atendido: 'Ramiro Estavillo',
      fam: 'Puertas', sub: 'Simple',
      title: 'Puerta simple — oficina',
      desc: 'Puerta simple en marco estándar con relleno de chapa y cerradura común. Terminación DTM.',
      dims: [['Ancho', '0,90 m'], ['Altura', '2,05 m'], ['Hojas', '1'], ['Marco', 'Estándar']],
      items: [['Fabricación — puerta simple', 'marco estándar + chapa, DTM', 64000], ['Cerradura común', 'con manija de aluminio', 6400], ['Colocación — incluida (simple)', 'montaje y ajuste', 5000]],
      sub: 75400, total: 92000,
      calc: { ancho: '0,90', alt: '2,05', av: '0,90', term: '1.00', coloc: '0.10', zona: 'Montevideo', tipo: 'Particular', baranda: false, contra: false, piso: false, led: false, ovr: '' }
    },
    'GV-A-00137': {
      num: 'PRE-2026-0137', status: 'appr', date: '02/06/2026',
      client: 'Carlos Gómez', ctype: 'Particular', contact: '098 220 145', obra: 'Casa, Solymar', atendido: 'Ramiro Estavillo',
      fam: 'Barandas', sub: 'Balcón',
      title: 'Baranda de terraza',
      desc: 'Baranda de terraza en caño y planchuela con pasamanos continuo y montantes verticales. Terminación pintura especial.',
      dims: [['Largo', '6,00 m'], ['Altura', '1,10 m'], ['Módulos', '4'], ['Lado', 'Terraza']],
      items: [['Fabricación — baranda de terraza', 'caño y planchuela, pintura especial', 86500], ['Anclajes a losa', 'planchuela de fijación', 5600], ['Colocación — incluida (simple)', 'montaje y nivelación', 5400]],
      sub: 97500, total: 118900,
      calc: { ancho: '0,90', alt: '1,10', av: '6,00', term: '1.25', coloc: '0.10', zona: 'Canelones', tipo: 'Particular', baranda: false, contra: false, piso: false, led: false, ovr: '' }
    }
  };

  window.RE_STATUS = {
    draft: { label: 'Borrador — sin enviar', badge: 'warn', dot: 'd-draft' },
    sent: { label: 'Enviado', badge: '', dot: 'd-sent' },
    appr: { label: 'Aprobado', badge: 'ok', dot: 'd-appr' },
    rej: { label: 'Rechazado', badge: '', dot: 'd-rej' },
    cancel: { label: 'Cancelado', badge: 'cancel', dot: 'd-cancel' }
  };
})();
