# Service Repository Architecture - BPS PHP Backend

## Overview

The Laravel backend has been refactored from a basic MVC structure to a **Service Repository Architecture**. This clean architecture pattern provides:

- ✅ **Clear separation of concerns** - Each layer has a specific responsibility
- ✅ **Testability** - Easier to unit test business logic
- ✅ **Maintainability** - Code is organized, reusable, and easy to understand
- ✅ **Scalability** - Easy to extend with new features without affecting existing code
- ✅ **Dependency Injection** - Loose coupling between components

## Architecture Layers

```
┌─────────────────────────┐
│      Controllers        │  (HTTP Requests/Responses)
│      (API Layer)        │
└────────────┬────────────┘
             │
┌────────────▼────────────┐
│      Services           │  (Business Logic)
│   (Application Layer)   │
└────────────┬────────────┘
             │
┌────────────▼────────────┐
│    Repositories         │  (Data Access)
│   (Persistence Layer)   │
└────────────┬────────────┘
             │
┌────────────▼────────────┐
│      Models             │  (Database)
│   (Eloquent Models)     │
└─────────────────────────┘
```

## Directory Structure

```
app/
├── Http/
│   ├── Controllers/
│   │   └── API/              # Clean controllers - minimal logic
│   │       ├── PatientController.php
│   │       ├── BillController.php
│   │       ├── PaymentController.php
│   │       └── ...
│   └── Middleware/
│       └── Validation/        # Validation-only middleware
│           ├── ValidatePatient.php
│           ├── ValidateBill.php
│           ├── ValidatePayment.php
│           └── ...
├── Repositories/            # Data access layer
│   ├── BaseRepository.php
│   ├── PatientRepository.php
│   ├── BillRepository.php
│   ├── PaymentRepository.php
│   └── ...
├── Services/               # Business logic layer
│   ├── PatientService.php
│   ├── BillService.php
│   ├── PaymentService.php
│   └── ...
└── Models/                 # Database models
    ├── patient.php
    ├── Bill.php
    ├── Payment.php
    └── ...
```

## Layer Responsibilities

### 1. Controllers (HTTP Layer)
**Location:** `app/Http/Controllers/API/`

Controllers handle HTTP requests and responses. They:
- Accept HTTP requests
- Delegate to services
- Return responses

**Example:**
```php
class PatientController extends Controller
{
    protected $patientService;

    public function __construct(PatientService $patientService)
    {
        $this->patientService = $patientService;
    }

    public function index(Request $request)
    {
        $patients = $this->patientService->getPatients(
            $request->input('search'),
            $request->input('limit', 10)
        );
        return response()->json($patients);
    }
}
```

### 2. Services (Business Logic Layer)
**Location:** `app/Services/`

Services contain all business logic. They:
- Execute complex operations
- Handle calculations and transformations
- Orchestrate multiple repositories
- Implement business rules

**Example:**
```php
class BillService
{
    protected $billRepository;

    public function __construct(BillRepository $billRepository)
    {
        $this->billRepository = $billRepository;
    }

    public function calculateBillAmount($grossCharges, $insurance, $adjustments, $taxes)
    {
        return round($grossCharges - $insurance - $adjustments + $taxes, 2);
    }

    public function createBill(array $data)
    {
        $billAmount = $this->calculateBillAmount(
            $data['grossCharges'],
            $data['insuranceCredit'],
            $data['adjustments'],
            $data['taxAndSurcharges']
        );

        return $this->billRepository->create([
            'bill_number' => $this->generateBillNumber(),
            'bill_amount' => $billAmount,
            // ... other fields
        ]);
    }
}
```

### 3. Repositories (Data Access Layer)
**Location:** `app/Repositories/`

Repositories handle all database operations. They:
- Encapsulate query logic
- Provide a consistent interface for data access
- Reduce code duplication

**Example:**
```php
class BillRepository extends BaseRepository
{
    public function getPaginatedWithSearch($search = null, $status = null, $perPage = 10)
    {
        $query = $this->model->select([...fields...])
            ->with([...relations...]);

        if ($search) {
            $query->where(function ($q) use ($search) {
                // search logic
            });
        }

        if ($status) {
            $query->where('status', $status);
        }

        return $query->latest()->paginate($perPage);
    }
}
```

### 4. Middleware / Validation Layer
**Location:** `app/Http/Middleware/Validation/`

Validation middleware handle input validation only:
- Validate request data
- Return validation errors
- Pass validated data to the controller

**Example:**
```php
class ValidateBill
{
    public function handle(Request $request, Closure $next)
    {
        if ($request->isMethod('post') || $request->isMethod('put')) {
            $request->validate([
                'visit_id' => 'required|integer|exists:visits,id',
                'grossCharges' => 'required|numeric|min:0',
                'dueDate' => 'required|date',
                // ... other validations
            ]);
        }

        return $next($request);
    }
}
```

## Data Flow

### Create Bill Example

```
1. Client sends POST /api/bills
                            ↓
2. ValidateBill Middleware
   - Validates request data
   - Returns error if invalid
   - Passes to controller if valid
                            ↓
3. BillController::store()
   - Receives validated data
   - Calls BillService
   - Returns response
                            ↓
4. BillService::createBill()
   - Calculates bill amount
   - Generates bill number
   - Calls BillRepository
                            ↓
5. BillRepository::create()
   - Prepares data
   - Calls Model create
   - Returns created record
                            ↓
6. Bill Model
   - Saves to database
   - Returns bill instance
                            ↓
7. Response sent to client
```

## How to Use

### Adding Validation

To require validation for an endpoint, register middleware in `routes/api.php`:

```php
Route::post('patients', [PatientController::class, 'store'])
    ->middleware('validation.patient');
```

### Creating New Features

**Step 1:** Create Repository (if needed)
```php
// app/Repositories/YourRepository.php
class YourRepository extends BaseRepository
{
    public function __construct(YourModel $model)
    {
        parent::__construct($model);
    }
}
```

**Step 2:** Create Service
```php
// app/Services/YourService.php
class YourService
{
    protected $repository;

    public function __construct(YourRepository $repository)
    {
        $this->repository = $repository;
    }

    public function yourMethod()
    {
        // Business logic here
        return $this->repository->create($data);
    }
}
```

**Step 3:** Create Validation Middleware (Optional)
```php
// app/Http/Middleware/Validation/ValidateYour.php
class ValidateYour
{
    public function handle(Request $request, Closure $next)
    {
        $request->validate([
            'field' => 'required|...',
        ]);
        return $next($request);
    }
}
```

**Step 4:** Create/Update Controller
```php
// app/Http/Controllers/API/YourController.php
class YourController extends Controller
{
    protected $yourService;

    public function __construct(YourService $yourService)
    {
        $this->yourService = $yourService;
    }

    public function store(Request $request)
    {
        $result = $this->yourService->yourMethod($request->all());
        return response()->json($result, 201);
    }
}
```

## Services Available

| Service | Purpose |
|---------|---------|
| `PatientService` | Patient CRUD and search |
| `BillService` | Bill creation, updates, calculations |
| `PaymentService` | Payment processing with transactions |
| `DocumentService` | Document management |
| `VisitService` | Visit data retrieval |
| `ProcedureCodeService` | Procedure code management |
| `AccidentDetailsService` | Accident details management |
| `InsuranceService` | Insurance records |
| `DashboardService` | Dashboard statistics |

## Validation Middleware Available

| Middleware | Path |
|-----------|------|
| `ValidatePatient` | `app/Http/Middleware/Validation/ValidatePatient.php` |
| `ValidateBill` | `app/Http/Middleware/Validation/ValidateBill.php` |
| `ValidatePayment` | `app/Http/Middleware/Validation/ValidatePayment.php` |
| `ValidateProcedureCode` | `app/Http/Middleware/Validation/ValidateProcedureCode.php` |
| `ValidateDocument` | `app/Http/Middleware/Validation/ValidateDocument.php` |

## Benefits of This Architecture

### 1. **Separation of Concerns**
- Controllers handle HTTP
- Services handle business logic
- Repositories handle data access
- Validation middleware handle validation

### 2. **Code Reusability**
- Services can be used by multiple controllers
- Repositories can be used by multiple services
- Easy to share logic

### 3. **Testability**
```php
class BillServiceTest extends TestCase
{
    public function test_bill_calculation()
    {
        $service = new BillService(new BillRepository(new Bill()));
        $amount = $service->calculateBillAmount(100, 10, 5, 5);
        $this->assertEquals(90, $amount);
    }
}
```

### 4. **Maintainability**
- Clear structure makes it easy to find code
- Changes are isolated to specific layers
- Less side effects

### 5. **Scalability**
- Easy to add caching in repositories
- Easy to add logging in services
- Easy to add events or notifications

## Common Patterns

### Using Multiple Repositories in a Service
```php
class PaymentService
{
    protected $paymentRepository;
    protected $billRepository;

    public function __construct(
        PaymentRepository $paymentRepository,
        BillRepository $billRepository
    ) {
        $this->paymentRepository = $paymentRepository;
        $this->billRepository = $billRepository;
    }

    public function processPayment($data)
    {
        $payment = $this->paymentRepository->create($data);
        $this->billRepository->updateBillStatus($data['bill_id']);
        return $payment;
    }
}
```

### Exception Handling in Services
```php
public function deletePatient($id)
{
    try {
        return $this->patientRepository->delete($id);
    } catch (ModelNotFoundException $e) {
        throw new \Exception('Patient not found');
    }
}
```

### Transactions in Services
```php
use Illuminate\Support\Facades\DB;

public function bulkCreateBills($billsData)
{
    return DB::transaction(function () use ($billsData) {
        $bills = [];
        foreach ($billsData as $data) {
            $bills[] = $this->createBill($data);
        }
        return $bills;
    });
}
```

## Migration from Old Code

All existing functionality has been preserved. The key changes are:

- Business logic moved to Services
- Data access moved to Repositories
- Validation moved to Middleware
- Controllers are now thin and clean

The functionality remains exactly the same - only the organization has improved.

## Next Steps

1. Register validation middleware in `routes/api.php` for endpoints
2. Add caching layer if performance tuning needed
3. Add event listeners for important operations
4. Create additional repositories for complex queries
5. Write unit tests for services

## Support

For questions or issues with the new architecture, refer to:
- Service files in `app/Services/`
- Repository files in `app/Repositories/`
- Middleware files in `app/Http/Middleware/Validation/`
