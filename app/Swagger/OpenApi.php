<?php

namespace App\Swagger;

use OpenApi\Annotations as OA;

/**
 * @OA\Info(
 *     title="Learning Math API",
 *     version="1.0.0",
 *     description="Документация для Learning Math API (Laravel + Swagger)",
 *     @OA\Contact(
 *         email="support@learning-math.com",
 *         name="API Support"
 *     )
 * )
 *
 * @OA\Server(
 *     url="https://learning-math:7890",
 *     description="Local development server"
 * )
 *
 * @OA\Tag(
 *     name="Auth",
 *     description="Эндпоинты для регистрации и авторизации"
 * )
 *
 * @OA\SecurityScheme(
 *     securityScheme="bearerAuth",
 *     type="http",
 *     scheme="bearer",
 *     bearerFormat="JWT"
 * )
 */
class OpenApi {}
