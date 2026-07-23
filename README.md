# Ágora

Una aplicación web para gestionar jornadas de capacitación. Comisionado por el Instituto de Profesorado Sedes Sapientiae.

Actualmente en desarrollo, vea las [Incidencias](https://github.com/Agora-Sedes/Agora/issues) y las [Solicitudes de Cambios](https://github.com/Agora-Sedes/Agora/pulls) para ver el progreso actual.

## Funcionalidades

- Inscripción de oyentes
  - Formulario que solicita datos de identificación y contacto
  - Pago con Mercado Pago, tarjeta de débito o crédito, en RapiPago y más utilizando la API de Mercado Pago
  - Capacidad de inscribir más de un oyente a la vez
  - Verificación de pagos presenciales mediante códigos QR
  - Verificación de asistencias presenciales mediante códigos QR
  - Verificación de certificados mediante códigos QR
- Panel de administradores
  - Inscripción de oyentes de último momento (cobro físico el mismo día de la charla, sin QR de verificación)
  - Editor de información sobre jornadas y charlas
  - Generación de certificados de asistencia, con QR verificador
  - Registros de jornadas anteriores, en caso de requerir verificación por parte de un tercero autorizado
  - Gestión de streaming para conferencias virtuales, incluyendo soporte para preguntas

## Ejecución

Utilizamos [Podman](https://podman.io/) con contenedores con [Alpine Linux](https://www.alpinelinux.org/) para garantizar entornos reproducibles. Es posible utilizar [Docker](https://docker.com) en lugar de Podman.

Apenas clone el repositorio y cada vez que se añada una nueva dependencia, ejecute los siguientes comandos para descargar las dependencias del proyecto y preparar el contenedor que eventualmente ejecutará la aplicación:

```sh
podman run --rm -it -v .:/app -w /app alpine:latest sh install-dependencies.sh

podman build --format docker --squash --tag localhost/agora_app:latest .
```

Una vez completado esto y cada vez que quiera iniciar el proyecto, utilice el siguiente comando:

```sh
# Para el entorno de producción:
podman compose -f docker-compose.production.yaml up

# Para el entorno de desarrollo:
podman compose up
```

El servicio toma un poco menos de un minuto para iniciar completamente. Presione Control+C para detener todo.

En el archivo `.env` se encuentra todo lo relevante para el despliegue. Nótese que `mailpit` se inicia en entornos de producción para simplificar pruebas.

Los datos guardados en el entorno de producción persisten indefinidamente (`podman volume rm agora_production-database-data` para borrarlos). Los datos guardados en el entorno de desarrollo se borran en cada inicio, reemplazándose por datos de ejemplo.
