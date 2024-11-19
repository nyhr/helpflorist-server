<?php
// Documentation: https://docs.nyhr.dev/api/v1

header("Content-Type: application/json");

// echo schemas
echo json_encode([
    "schemas" => [
        "applications" => [
            "id" => "integer",
            "name" => "string",
            "version" => "string",
            "type" => "string",
            "download_url" => "string",
            "created_by" => "integer",
            "created_at" => "string",
            "updated_by" => "integer",
            "updated_at" => "string"
        ],
        "users" => [
            "id" => "integer",
            "username" => "string",
            "password" => "string",
            "email" => "string",
            "role_id" => "integer",
            "created_at" => "string",
            "updated_at" => "string"
        ],
        "roles" => [
            "id" => "integer",
            "name" => "string"
        ]
    ],
    "responses" => [
        "success" => [
            "200" => [
                "message" => "string",
                "data" => "object | array (optional)"
            ],
            "201" => [
                "message" => "string",
                "data" => "object"
            ]
        ],
        "error" => [
            "400" => [
                "message" => "Bad Request",
                "data" => "error message (development only)"
            ],
            "401" => [
                "message" => "Unauthorized"
            ],
            "403" => [
                "message" => "Forbidden"
            ],
            "404" => [
                "message" => "Not Found"
            ],
            "405" => [
                "message" => "Method Not Allowed"
            ],
            "500" => [
                "message" => "Internal Server Error",
                "data" => "error message (development only)"
            ]
        ]
    ],
    "routes" => [
        "health" => [
            "GET" => [
                "protected" => false,
                "required_role" => 0,
                "description" => "Check if the API is running",
                "response" => [
                    "success" => [
                        "200" => [
                            "message" => "OK",
                            "data" => null
                        ]
                    ],
                    "error" => [
                        "500" => [
                            "message" => "Internal Server Error",
                            "data" => "error message (development only)"
                        ]
                    ]
                ]
            ]
        ],
        "auth" => [
            "POST" => [
                "protected" => false,
                "required_role" => 0,
                "body" => [
                    "username" => "string",
                    "password" => "string"
                ],
                "description" => "Login to get an authentication token",
                "response" => [
                    "success" => [
                        "200" => [
                            "message" => "Login successful",
                            "data" => null
                        ]
                    ],
                    "error" => [
                        "400" => [
                            "message" => "Bad Request",
                            "data" => "Invalid input data (development only)"
                        ],
                        "401" => [
                            "message" => "Unauthorized"
                        ],
                        "500" => [
                            "message" => "Internal Server Error",
                            "data" => "error message (development only)"
                        ]
                    ]
                ]
            ],
            "PUT" => [
                "protected" => true,
                "required_role" => 1,
                "description" => "Refresh the authentication token",
                "response" => [
                    "success" => [
                        "200" => [
                            "message" => "Token refreshed successfully",
                            "data" => null
                        ]
                    ],
                    "error" => [
                        "401" => [
                            "message" => "Unauthorized"
                        ],
                        "403" => [
                            "message" => "Forbidden"
                        ],
                        "500" => [
                            "message" => "Internal Server Error",
                            "data" => "error message (development only)"
                        ]
                    ]
                ]
            ],
            "DELETE" => [
                "protected" => false,
                "required_role" => 0,
                "description" => "Logout and remove the authentication token",
                "response" => [
                    "success" => [
                        "200" => [
                            "message" => "Logout successful",
                            "data" => null
                        ]
                    ],
                    "error" => [
                        "500" => [
                            "message" => "Internal Server Error",
                            "data" => "error message (development only)"
                        ]
                    ]
                ]
            ],
            "GET" => [
                "protected" => true,
                "required_role" => 1,
                "description" => "Verify the current authentication token",
                "response" => [
                    "success" => [
                        "200" => [
                            "message" => "Token verified successfully",
                            "data" => null
                        ]
                    ],
                    "error" => [
                        "401" => [
                            "message" => "Unauthorized"
                        ],
                        "403" => [
                            "message" => "Forbidden"
                        ],
                        "500" => [
                            "message" => "Internal Server Error",
                            "data" => "error message (development only)"
                        ]
                    ]
                ]
            ]
        ],
        "users" => [
            "GET" => [
                "protected" => true,
                "required_role" => 2,
                "conditions" => [
                    "?id=[integer]" => [
                        "description" => "Get a user by id",
                        "example" => "/v1/users?id=1"
                    ],
                    "?id=all | null" => [
                        "description" => "Get all users",
                        "example" => "/v1/users?id=all | /v1/users"
                    ]
                ],
                "description" => "Retrieve user information",
                "response" => [
                    "success" => [
                        "200" => [
                            "message" => "Users retrieved successfully",
                            "data" => "user object | array of users"
                        ]
                    ],
                    "error" => [
                        "401" => [
                            "message" => "Unauthorized"
                        ],
                        "403" => [
                            "message" => "Forbidden"
                        ],
                        "404" => [
                            "message" => "Not Found"
                        ],
                        "500" => [
                            "message" => "Internal Server Error",
                            "data" => "error message (development only)"
                        ]
                    ]
                ]
            ],
            "POST" => [
                "protected" => true,
                "required_role" => 2,
                "body" => [
                    "username" => "string",
                    "email" => "string",
                    "password" => "string",
                    "role_id" => "integer"
                ],
                "description" => "Create a new user",
                "response" => [
                    "success" => [
                        "201" => [
                            "message" => "User created successfully",
                            "data" => "created user object"
                        ]
                    ],
                    "error" => [
                        "400" => [
                            "message" => "Bad Request",
                            "data" => "Invalid input data or email already exists (development only)"
                        ],
                        "401" => [
                            "message" => "Unauthorized"
                        ],
                        "403" => [
                            "message" => "Forbidden"
                        ],
                        "500" => [
                            "message" => "Internal Server Error",
                            "data" => "error message (development only)"
                        ]
                    ]
                ]
            ],
            "PUT" => [
                "protected" => true,
                "required_role" => 2,
                "body" => [
                    "id" => "integer",
                    "username" => "string (optional)",
                    "email" => "string (optional)",
                    "password" => "string (optional)",
                    "role_id" => "integer (optional)"
                ],
                "description" => "Update an existing user",
                "response" => [
                    "success" => [
                        "200" => [
                            "message" => "User updated successfully",
                            "data" => "updated user object"
                        ]
                    ],
                    "error" => [
                        "400" => [
                            "message" => "Bad Request",
                            "data" => "Invalid input data (development only)"
                        ],
                        "401" => [
                            "message" => "Unauthorized"
                        ],
                        "403" => [
                            "message" => "Forbidden"
                        ],
                        "404" => [
                            "message" => "Not Found"
                        ],
                        "500" => [
                            "message" => "Internal Server Error",
                            "data" => "error message (development only)"
                        ]
                    ]
                ]
            ],
            "DELETE" => [
                "protected" => true,
                "required_role" => 3,
                "body" => [
                    "id" => "integer"
                ],
                "description" => "Delete a user",
                "response" => [
                    "success" => [
                        "200" => [
                            "message" => "User deleted successfully",
                            "data" => null
                        ]
                    ],
                    "error" => [
                        "400" => [
                            "message" => "Bad Request",
                            "data" => "Invalid user ID (development only)"
                        ],
                        "401" => [
                            "message" => "Unauthorized"
                        ],
                        "403" => [
                            "message" => "Forbidden"
                        ],
                        "404" => [
                            "message" => "Not Found"
                        ],
                        "500" => [
                            "message" => "Internal Server Error",
                            "data" => "error message (development only)"
                        ]
                    ]
                ]
            ]
        ],
        "roles" => [
            "GET" => [
                "protected" => true,
                "required_role" => 2,
                "conditions" => [
                    "?id=[integer]" => [
                        "description" => "Get a role by id",
                        "example" => "/v1/roles?id=1"
                    ],
                    "?id=all | null" => [
                        "description" => "Get all roles",
                        "example" => "/v1/roles?id=all | /v1/roles"
                    ]
                ],
                "description" => "Retrieve role information",
                "response" => [
                    "success" => [
                        "200" => [
                            "message" => "Roles retrieved successfully",
                            "data" => "role object | array of roles"
                        ]
                    ],
                    "error" => [
                        "401" => [
                            "message" => "Unauthorized"
                        ],
                        "403" => [
                            "message" => "Forbidden"
                        ],
                        "404" => [
                            "message" => "Not Found"
                        ],
                        "500" => [
                            "message" => "Internal Server Error",
                            "data" => "error message (development only)"
                        ]
                    ]
                ]
            ],
            "POST" => [
                "protected" => true,
                "required_role" => 2,
                "body" => [
                    "name" => "string"
                ],
                "description" => "Create a new role",
                "response" => [
                    "success" => [
                        "201" => [
                            "message" => "Role created successfully",
                            "data" => "created role object"
                        ]
                    ],
                    "error" => [
                        "400" => [
                            "message" => "Bad Request",
                            "data" => "Invalid input data or role already exists (development only)"
                        ],
                        "401" => [
                            "message" => "Unauthorized"
                        ],
                        "403" => [
                            "message" => "Forbidden"
                        ],
                        "500" => [
                            "message" => "Internal Server Error",
                            "data" => "error message (development only)"
                        ]
                    ]
                ]
            ],
            "PUT" => [
                "protected" => true,
                "required_role" => 2,
                "body" => [
                    "id" => "integer",
                    "name" => "string (optional)"
                ],
                "description" => "Update an existing role",
                "response" => [
                    "success" => [
                        "200" => [
                            "message" => "Role updated successfully",
                            "data" => "updated role object"
                        ]
                    ],
                    "error" => [
                        "400" => [
                            "message" => "Bad Request",
                            "data" => "Invalid input data (development only)"
                        ],
                        "401" => [
                            "message" => "Unauthorized"
                        ],
                        "403" => [
                            "message" => "Forbidden"
                        ],
                        "404" => [
                            "message" => "Not Found"
                        ],
                        "500" => [
                            "message" => "Internal Server Error",
                            "data" => "error message (development only)"
                        ]
                    ]
                ]
            ],
            "DELETE" => [
                "protected" => true,
                "required_role" => 3,
                "body" => [
                    "id" => "integer"
                ],
                "description" => "Delete a role",
                "response" => [
                    "success" => [
                        "200" => [
                            "message" => "Role deleted successfully",
                            "data" => null
                        ]
                    ],
                    "error" => [
                        "400" => [
                            "message" => "Bad Request",
                            "data" => "Invalid role ID (development only)"
                        ],
                        "401" => [
                            "message" => "Unauthorized"
                        ],
                        "403" => [
                            "message" => "Forbidden"
                        ],
                        "404" => [
                            "message" => "Not Found"
                        ],
                        "500" => [
                            "message" => "Internal Server Error",
                            "data" => "error message (development only)"
                        ]
                    ]
                ]
            ]
        ],
        "applications" => [
            "GET" => [
                "protected" => false,
                "required_role" => 0,
                "conditions" => [
                    "?id=[integer]" => [
                        "description" => "Get an application by id",
                        "example" => "/v1/applications?id=1"
                    ],
                    "?id=all | null" => [
                        "description" => "Get all applications",
                        "example" => "/v1/applications?id=all | /v1/applications"
                    ]
                ],
                "description" => "Retrieve application information",
                "response" => [
                    "success" => [
                        "200" => [
                            "message" => "Applications retrieved successfully",
                            "data" => "application object | array of applications"
                        ]
                    ],
                    "error" => [
                        "404" => [
                            "message" => "Not Found"
                        ],
                        "500" => [
                            "message" => "Internal Server Error",
                            "data" => "error message (development only)"
                        ]
                    ]
                ]
            ],
            "POST" => [
                "protected" => true,
                "required_role" => 2,
                "body" => [
                    "name" => "string",
                    "version" => "string",
                    "type" => "string",
                    "download_url" => "string"
                ],
                "description" => "Create a new application",
                "response" => [
                    "success" => [
                        "201" => [
                            "message" => "Application created successfully",
                            "data" => "created application object"
                        ]
                    ],
                    "error" => [
                        "400" => [
                            "message" => "Bad Request",
                            "data" => "Invalid input data or application already exists (development only)"
                        ],
                        "401" => [
                            "message" => "Unauthorized"
                        ],
                        "403" => [
                            "message" => "Forbidden"
                        ],
                        "500" => [
                            "message" => "Internal Server Error",
                            "data" => "error message (development only)"
                        ]
                    ]
                ]
            ],
            "PUT" => [
                "protected" => true,
                "required_role" => 2,
                "body" => [
                    "id" => "integer",
                    "name" => "string (optional)",
                    "version" => "string (optional)",
                    "type" => "string (optional)",
                    "download_url" => "string (optional)"
                ],
                "description" => "Update an existing application",
                "response" => [
                    "success" => [
                        "200" => [
                            "message" => "Application updated successfully",
                            "data" => "updated application object"
                        ]
                    ],
                    "error" => [
                        "400" => [
                            "message" => "Bad Request",
                            "data" => "Invalid input data (development only)"
                        ],
                        "401" => [
                            "message" => "Unauthorized"
                        ],
                        "403" => [
                            "message" => "Forbidden"
                        ],
                        "404" => [
                            "message" => "Not Found"
                        ],
                        "500" => [
                            "message" => "Internal Server Error",
                            "data" => "error message (development only)"
                        ]
                    ]
                ]
            ],
            "DELETE" => [
                "protected" => true,
                "required_role" => 3,
                "body" => [
                    "id" => "integer"
                ],
                "description" => "Delete an application",
                "response" => [
                    "success" => [
                        "200" => [
                            "message" => "Application deleted successfully",
                            "data" => null
                        ]
                    ],
                    "error" => [
                        "400" => [
                            "message" => "Bad Request",
                            "data" => "Invalid application ID (development only)"
                        ],
                        "401" => [
                            "message" => "Unauthorized"
                        ],
                        "403" => [
                            "message" => "Forbidden"
                        ],
                        "404" => [
                            "message" => "Not Found"
                        ],
                        "500" => [
                            "message" => "Internal Server Error",
                            "data" => "error message (development only)"
                        ]
                    ]
                ]
            ]
        ]
    ]
]);
?>