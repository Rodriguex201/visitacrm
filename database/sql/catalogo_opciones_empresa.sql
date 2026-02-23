-- Crear catálogo de opciones para perfil comercial por empresa
CREATE TABLE IF NOT EXISTS catalogo_opciones (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    categoria VARCHAR(120) NOT NULL,
    nombre VARCHAR(255) NOT NULL,
    activo TINYINT(1) NOT NULL DEFAULT 1,
    created_at TIMESTAMP NULL DEFAULT NULL,
    updated_at TIMESTAMP NULL DEFAULT NULL,
    UNIQUE KEY uq_catalogo_opciones_categoria_nombre (categoria, nombre)
);

CREATE TABLE IF NOT EXISTS empresa_opcion (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    empresa_id BIGINT UNSIGNED NOT NULL,
    opcion_id BIGINT UNSIGNED NOT NULL,
    created_at TIMESTAMP NULL DEFAULT NULL,
    updated_at TIMESTAMP NULL DEFAULT NULL,
    UNIQUE KEY uq_empresa_opcion_empresa_opcion (empresa_id, opcion_id),
    CONSTRAINT fk_empresa_opcion_empresa FOREIGN KEY (empresa_id) REFERENCES empresas(id) ON DELETE CASCADE,
    CONSTRAINT fk_empresa_opcion_opcion FOREIGN KEY (opcion_id) REFERENCES catalogo_opciones(id) ON DELETE CASCADE
);

-- Insertar opciones iniciales (sin duplicar)
INSERT INTO catalogo_opciones (categoria, nombre, activo, created_at, updated_at)
SELECT * FROM (
    SELECT 'Estado Actual', 'Negocio Nuevo', 1, NOW(), NOW() UNION ALL
    SELECT 'Estado Actual', 'Manual', 1, NOW(), NOW() UNION ALL
    SELECT 'Estado Actual', 'Otro Software', 1, NOW(), NOW() UNION ALL

    SELECT 'Aplicativos', 'CAO Total Nube', 1, NOW(), NOW() UNION ALL
    SELECT 'Aplicativos', 'CAO Total Local', 1, NOW(), NOW() UNION ALL
    SELECT 'Aplicativos', 'POS Básico Nube', 1, NOW(), NOW() UNION ALL
    SELECT 'Aplicativos', 'Parqueadero', 1, NOW(), NOW() UNION ALL
    SELECT 'Aplicativos', 'Taller', 1, NOW(), NOW() UNION ALL

    SELECT 'Procesos Electrónicos', 'Fra Electronica', 1, NOW(), NOW() UNION ALL
    SELECT 'Procesos Electrónicos', 'Nomina Electronica', 1, NOW(), NOW() UNION ALL
    SELECT 'Procesos Electrónicos', 'Documento Soporte', 1, NOW(), NOW() UNION ALL
    SELECT 'Procesos Electrónicos', 'Eventos', 1, NOW(), NOW() UNION ALL

    SELECT 'Equipos', 'CPU', 1, NOW(), NOW() UNION ALL
    SELECT 'Equipos', 'Monitor', 1, NOW(), NOW() UNION ALL
    SELECT 'Equipos', 'Teclado-Mouse', 1, NOW(), NOW() UNION ALL
    SELECT 'Equipos', 'Impresora POS', 1, NOW(), NOW() UNION ALL
    SELECT 'Equipos', 'Lector Codigo De Barras', 1, NOW(), NOW() UNION ALL
    SELECT 'Equipos', 'UPS', 1, NOW(), NOW() UNION ALL
    SELECT 'Equipos', 'Otro', 1, NOW(), NOW()
) AS opciones(categoria, nombre, activo, created_at, updated_at)
WHERE NOT EXISTS (
    SELECT 1
    FROM catalogo_opciones c
    WHERE c.categoria = opciones.categoria
      AND c.nombre = opciones.nombre
);
