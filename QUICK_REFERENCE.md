# Quick Reference - Service Repository Architecture

## File Locations

### Controllers
```
app/Http/Controllers/API/
├── PatientController.php
├── BillController.php
├── PaymentController.php
├── DocumentController.php
├── VisitController.php
├── CodeController.php
├── AccidentDetailsController.php
├── InsuranceController.php
└── DashboardController.php
```

### Services
```
app/Services/
├── PatientService.php
├── BillService.php
├── PaymentService.php
├── DocumentService.php
├── VisitService.php
├── ProcedureCodeService.php
├── AccidentDetailsService.php
├── InsuranceService.php
└── DashboardService.php
```

### Repositories
```
app/Repositories/
├── BaseRepository.php
├── PatientRepository.php
├── BillRepository.php
├── PaymentRepository.php
├── DocumentRepository.php
├── VisitRepository.php
├── ProcedureCodeRepository.php
├── AccidentDetailsRepository.php
└── InsuranceRepository.php
```

### Validation Middleware
```
app/Http/Middleware/Validation/
├── ValidatePatient.php
├── ValidateBill.php
├── ValidatePayment.php
├── ValidateProcedureCode.php
└── ValidateDocument.php
```

## Quick Code Examples

### Adding a Service Method
```php
// In PatientService
public function findPatientByEmail($email)
{
    return $this->patientRepository->query()
        ->where('email', $email)
        ->firstOrFail();
}
```

### Adding a Repository Method
```php
// In PatientRepository
public function findByIdWithCases($id)
{
    return $this->model->with(['cases'])->findOrFail($id);
}
```

### Adding Validation Middleware
```php
// In routes/api.php
Route::post('patients', [PatientController::class, 'store'])
    ->middleware('validation.patient');
```

### Using Service in Controller
```php
public function store(Request $request)
{
    try {
        $patient = $this->patientService->createPatient($request->all());
        return response()->json($patient, 201);
    } catch (\Exception $e) {
        return response()->json(['error' => $e->getMessage()], 422);
    }
}
```

### Complex Service Method with Transactions
```php
use Illuminate\Support\Facades\DB;

public function processPaymentWithBillUpdate($paymentData)
{
    return DB::transaction(function () use ($paymentData) {
        $payment = $this->paymentRepository->create($paymentData);
        $bill = $this->billRepository->findById($paymentData['bill_id']);
        
        $bill->paid_amount += $paymentData['amount'];
        $bill->outstanding_amount -= $paymentData['amount'];
        $bill->save();
        
        return $payment;
    });
}
```

## Data Access Patterns

### Simple Query
```php
// In Service
$patient = $this->patientRepository->findById($id);
```

### Search with Pagination
```php
// In Service
$patients = $this->patientRepository->getPaginatedWithSearch('John', 10);
```

### With Relations
```php
// In Service
$patient = $this->patientRepository->getWithRelations($id);
```

### Custom Query
```php
// In Repository
public function getActivePatients()
{
    return $this->model->where('status', 'active')->get();
}

// In Service
public function getActivePatients()
{
    return $this->patientRepository->getActivePatients();
}
```

## Error Handling Pattern

```php
// In Controller
public function store(Request $request)
{
    try {
        $result = $this->service->create($request->all());
        return response()->json($result, 201);
    } catch (\Exception $e) {
        return response()->json([
            'message' => 'Error: ' . $e->getMessage()
        ], 422);
    }
}

// In Service
public function create($data)
{
    if (!$this->validateData($data)) {
        throw new \Exception('Invalid data');
    }
    
    return $this->repository->create($data);
}
```

## Testing Pattern

```php
// Test Service in isolation
class BillServiceTest extends TestCase
{
    private $billService;
    private $billRepository;

    public function setUp(): void
    {
        $this->billRepository = $this->createMock(BillRepository::class);
        $this->billService = new BillService($this->billRepository);
    }

    public function test_calculate_bill_amount()
    {
        $amount = $this->billService->calculateBillAmount(100, 20, 10, 5);
        $this->assertEquals(75, $amount);
    }
}
```

## Checklist for New Endpoints

- [ ] Create/Update Repository (if needed)
- [ ] Create/Update Service with business logic
- [ ] Create Validation Middleware (if input validation needed)
- [ ] Create/Update Controller to use Service
- [ ] Register routes in `routes/api.php`
- [ ] Register validation middleware in routes (if applicable)
- [ ] Test the endpoint
- [ ] Update documentation

## Common Mistakes to Avoid

❌ **Don't** put SQL logic in controllers
✅ **Do** put SQL logic in repositories

❌ **Don't** put business logic in repositories
✅ **Do** put business logic in services

❌ **Don't** call models directly from controllers
✅ **Do** use repositories to access models

❌ **Don't** validate in services
✅ **Do** validate in middleware or use Form Requests

❌ **Don't** mix validation with business logic in services
✅ **Do** keep validation separate in middleware

## Performance Tips

1. **Use Pagination** for large queries
```php
$bills = $this->billRepository->getPaginatedWithSearch($search, null, 20);
```

2. **Eager Load Relations** to avoid N+1 queries
```php
// In Repository
return $this->model->with(['bills', 'payments'])->paginate($perPage);
```

3. **Select Only Needed Fields**
```php
// In Repository
return $this->model->select(['id', 'name', 'email'])->get();
```

4. **Use Indexes** on frequently queried fields

5. **Cache Results** for read-heavy endpoints
```php
// In Service
return Cache::remember('patients', 3600, function () {
    return $this->patientRepository->all();
});
```

---

## Key Commands

```bash
# Create a new Service
php artisan make:service Services/YourService

# Create a new Repository
php artisan make:class Repositories/YourRepository

# Create a new Middleware
php artisan make:middleware Validation/ValidateYour

# Run tests
php artisan test

# Clear cache
php artisan cache:clear
```
