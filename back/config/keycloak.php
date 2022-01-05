<?php

return [
    'realm_public_key' => env('KEYCLOAK_REALM_PUBLIC_KEY', 'MIIBIjANBgkqhkiG9w0BAQEFAAOCAQ8AMIIBCgKCAQEAi8XXRw+AhmaD/9+u2FslR31L4ewh+v3sqRMj3q29cneA8hKdj7lia6d/qMPtqRyOOsNGQslnf2kVPTDYX7gRpv27csMbl4HQ2hQ6Zys+iaNMpvMnZcWlLTgAk+YI2xuPSvahSehM11v9nvzokXaiS0HTIRRqClhkhslW9caK/SRCrXvov1SunTx+YsrqjWwg19uEhxMWqNiaas52uwwrE4dUXrfh57K1gBHFoFbPJ+RlNe8TzyjCOLoby/hwZ8om2tcoSMHNMNorX1bIJbE4vx0EMs0yh3+VZlH/0FgbPWNoD+M6H+LIrJpz5Vrdz1DRTXv7REkCEj1ScD6OXqWzHQIDAQAB'),

    'load_user_from_database' => env('KEYCLOAK_LOAD_USER_FROM_DATABASE', false),

    'user_provider_credential' => env('KEYCLOAK_USER_PROVIDER_CREDENTIAL', 'email'),

    'token_principal_attribute' => env('KEYCLOAK_TOKEN_PRINCIPAL_ATTRIBUTE', 'preferred_username'),

    'append_decoded_token' => env('KEYCLOAK_APPEND_DECODED_TOKEN', false),

    'allowed_resources' => env('KEYCLOAK_ALLOWED_RESOURCES', 'hrmess')
];
