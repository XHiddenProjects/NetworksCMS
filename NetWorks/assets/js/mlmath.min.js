var MLMath = {
    ...Math,
    stats:{
        /**
          * Finds the average value of a set of numbers
          * @param  {...number} numbers Array of numbers to find the mean
          * @returns {Number} The mean of the numbers
          */
        mean: (...numbers)=>numbers.reduce((a,b)=>a+b)/numbers.length,
        /**
         * Finds the value that appears most frequently in a set of data
         * @param  {...Number} numbers 
         * @returns 
         */
        mode: (...numbers)=>{
            let mode = 0, max = 0;
            for(let i=0;i<numbers.length;i++){
                let count = 0;
                for(let j=0;j<numbers.length;j++){
                    if(numbers[j]===numbers[i]) count++;
                }
                if(count>max){
                    max = count;
                    mode = numbers[i];
                }
            }
            return mode;
        },
        /**
         * Finds the middle value of a set of numbers
         * @param  {...Number} numbers List of numbers
         * @returns {Number} The middle number
         */
        median: (...numbers)=>{
            numbers.sort((a,b)=>a-b);
            let mid = Math.floor(numbers.length/2);
            return numbers.length%2!==0 ? numbers[mid] : (numbers[mid-1]+numbers[mid])/2;
        },
        /**
         * Finds the difference between the highest/lowest values in a dataset.
         * @param  {...Number} numbers List of numbers
         * @returns {Number} The range of the numbers
         */
        range: (...numbers)=>Math.max(...numbers)-Math.min(...numbers),
        /**
         * Finds the standard deviation of a set of numbers
         * @param  {...Number} numbers List of numbers
         * @returns {Number} The standard deviation of the numbers
         */
        stdDev: (...numbers)=>{
            const mean = MLMath.stats.mean(...numbers),
            variance = MLMath.stats.mean(...numbers.map(x=>Math.pow(x-mean,2)));
            return Math.sqrt(variance);
        },
        /**
         * Finds the spread of the values
         * @param  {...Number} numbers List of numbers
         * @returns {Number} The variance of the numbers
         */
        var: (...numbers)=>{
            const mean = MLMath.stats.mean(...numbers);
            return MLMath.stats.mean(...numbers.map(x=>Math.pow(x-mean,2)));
        },
        /**
         * Finds the correlation coefficient between two sets of data
         * @param {Number[]} x 
         * @param {Number[]} y 
         * @returns {Number} The correlation coefficient
         */
        correl: (x,y)=>{
            const meanX = MLMath.stats.mean(...x),
            meanY = MLMath.stats.mean(...y),
            numerator = x.reduce((sum, xi, i) => sum + (xi - meanX) * (y[i] - meanY), 0),
            denominator = Math.sqrt(
                x.reduce((sum, xi) => sum + (xi - meanX) ** 2, 0) *
                y.reduce((sum, yi) => sum + (yi - meanY) ** 2, 0)
            );
            return numerator / denominator;
        },
        /**
         * Finds the populated covariance of two sets of data
         * @param {Number[]} x X-axis data
         * @param {Number[]} y Y-axis data
         * @returns {Number} The populated covariance of the data
         */
        COVARIANCE_P: (x,y)=>{
            const meanX = MLMath.stats.mean(...x),
            meanY = MLMath.stats.mean(...y),
            deviations = x.map((value, index) => (value - meanX) * (y[index] - meanY));
            return MLMath.stats.mean(...deviations);
        },
        /**
         * Finds the sample covariance of two sets of data
         * @param {Number[]} x X-axis data
         * @param {Number[]} y Y-axis data
         * @returns {Number} The sample covariance of the data
         */
        COVARIANCE_S: (x,y)=>{
            if (x.length!==y.length) throw new Error("Datasets must have the same length");
            const meanX = MLMath.stats.mean(...x),
            meanY = MLMath.stats.mean(...y),
            n = x.length;
            let covarianceSum = 0;
            for (let i = 0; i < n; i++) covarianceSum += (x[i] - meanX) * (y[i] - meanY);
            return covarianceSum / (n - 1);
        },
        /**
         * Returns the CHI-SQ Test
         * @param {Number[]} observed Numbers being observed
         * @param {Number[]} expected Expected Numbers
         * @returns {Number} Value of the test
         */
        CHISQ_TEST: (observed, expected)=>{
            if (observed.length !== expected.length) throw new Error("Observed and expected arrays must have the same length");
            let chiSq = 0;
            for (let i = 0; i < observed.length; i++) chiSq += Math.pow(observed[i] - expected[i], 2) / expected[i];
            return chiSq;
        },
        /**
         * Calculates the confidence interval for a population mean
         * @param {Number} alpha Significance level (e.g., 0.05 for 95% confidence)
         * @param {Number} stdDev Standard deviation of the population
         * @param {Number} size Sample size
         * @returns {Number} The confidence interval
         */
        CONFIDENCE_NORM: (alpha, stdDev, size) => {
            const z = MLMath.stats.Z_SCORE(alpha / 2);
            return parseFloat((z * (stdDev / Math.sqrt(size))).toFixed(3));
        },
        CONFIDENCE_T(alpha, standardDev, size) {
            const df = size - 1, // degrees of freedom
            alphaOverTwo = alpha / 2,
            standardError = standardDev / Math.sqrt(size),
            tCritical = MLMath.stats.T_SCORE(1 - alphaOverTwo, df),
            marginOfError = tCritical * standardError;
            return marginOfError;
        },
        /**
         * Calculates the Z-score for a given significance level
         * @param {Number} alpha Significance level (e.g., 0.025 for 95% confidence)
         * @returns {Number} The Z-score
         */
        Z_SCORE: (alpha) => {
            return -1.0 * MLMath.stats.invCumDist(alpha);
        },
        /**
         * Calculates the inverse cumulative distribution function for the standard normal distribution
         * @param {Number} p Probability
         * @returns {Number} The value of the inverse cumulative distribution function
         */
        invCumDist: (p) => {
            // Approximation using the Beasley-Springer-Moro algorithm
            const a = [2.50662823884, -18.61500062529, 41.39119773534, -25.44106049637],
            b = [-8.47351093090, 23.08336743743, -21.06224101826, 3.13082909833],
            c = [0.3374754822726147, 0.9761690190917186, 0.1607979714918209, 0.0276438810333863, 0.0038405729373609, 0.0003951896511919, 0.0000321767881768, 0.0000002888167364, 0.0000003960315187],
            x = p - 0.5;
            let r;
            if (Math.abs(x) < 0.42) {
                r = x * x;
                return x * (((a[3] * r + a[2]) * r + a[1]) * r + a[0]) / ((((b[3] * r + b[2]) * r + b[1]) * r + b[0]) * r + 1.0);
            } else {
                r = p;
                if (x > 0) r = 1.0 - p;
                r = Math.log(-Math.log(r));
                let result = c[0];
                for (let i = 1; i < c.length; i++) result += c[i] * Math.pow(r, i);
                if (x < 0) result = -result;
                return result;
            }
        },
        /**
         * Give you a number that describes the value that a given percent of the values are lower than.
         * @param {Number[]} nums List of numbers
         * @param {Number} percentage Percentage of lower values
         * @returns {Number}
         */
        percentile: (nums, percentage) => {
            nums.sort((a, b) => a - b);
            const index = (percentage / 100) * (nums.length - 1);
            if (Math.floor(index) === index) {
            return nums[index];
            } else {
            const lower = nums[Math.floor(index)];
            const upper = nums[Math.ceil(index)];
            return lower + (upper - lower) * (index - Math.floor(index));
            }
        }
    },
    random: {
        uniform: (min,max,size)=>{
            let i=0,r=0,c=0;
            const floats = [];
            if(Array.isArray(size)){
                do{
                    c=0;
                    floats.push([]);
                    do{
                        floats[r][c] = Math.random() * (max-min) + min;
                        c++;
                    }while(c<size[1]);
                    r++;
                }while(r<size[0]);
            }else{
                do{
                    floats.push(Math.random() * (max-min) + min);
                    i++;
                }while(i<size);
            }
            return floats;
        },
        normal: (mean = 0, stdDev = 1, size = 1) => {
            const generateNormal = () => {
                let u = 0, v = 0;
                while (u === 0) u = Math.random();
                while (v === 0) v = Math.random();
                return Math.sqrt(-2.0 * Math.log(u)) * Math.cos(2.0 * Math.PI * v);
            };

            if (Array.isArray(size)) {
                const result = [];
                for (let i = 0; i < size[0]; i++) {
                    const row = [];
                    for (let j = 0; j < size[1]; j++) {
                        row.push(mean + stdDev * generateNormal());
                    }
                    result.push(row);
                }
                return result;
            } else {
                const result = [];
                for (let i = 0; i < size; i++) {
                    result.push(mean + stdDev * generateNormal());
                }
                return result;
            }
        }
    },
    regressions:{
        /**
         * Gets the linear regression for x and y coords
         * @param {Number[]} x X-axis
         * @param {Number[]} y Y-axis
         * @returns {{slope: Number, intercept: Number}} Slope/Intercept calculated
         */
        linear: (x,y)=>{
            const n = x.length;
            if (n !== y.length) throw new Error("Datasets must have the same length");

            const meanX = MLMath.stats.mean(...x);
            const meanY = MLMath.stats.mean(...y);

            let numerator = 0;
            let denominator = 0;

            for (let i = 0; i < n; i++) {
                numerator += (x[i] - meanX) * (y[i] - meanY);
                denominator += Math.pow(x[i] - meanX, 2);
            }

            const slope = numerator / denominator;
            const intercept = meanY - slope * meanX;

            return { slope, intercept };
        },
        /**
         * Returns the polynomial
         * @param {Number[]} x X-axis
         * @param {Number[]} y Y-axis
         * @param {Number} degree Degree
         * @returns {Number} coefficients
         */
        polynomial:(x,y,degree=2)=>{
            const n = x.length;
            if (n !== y.length) throw new Error("Datasets must have the same length");
            const X = [];
            for (let i = 0; i < n; i++) {
                const row = [];
                for (let j = 0; j <= degree; j++) {
                row.push(Math.pow(x[i], j));
                }
                X.push(row);
            }
            const XT = X[0].map((_, colIndex) => X.map(row => row[colIndex]));
            const XTX = XT.map(row => row.map((_, colIndex) => row.reduce((sum, val, rowIndex) => sum + val * X[rowIndex][colIndex], 0)));
            const XTY = XT.map(row => row.reduce((sum, val, rowIndex) => sum + val * y[rowIndex], 0));
            const coefficients = MLMath.regressions.solve(XTX, XTY);
            return coefficients;
        },
        /**
         * Solves a system of linear equations using Gaussian elimination
         * @param {Number[][]} A Coefficient matrix
         * @param {Number[]} b Constant terms vector
         * @returns {Number[]} Solution vector
         */
        solve: (A, b) => {
            const n = A.length;
            for (let i = 0; i < n; i++) {
            let maxRow = i;
            for (let k = i + 1; k < n; k++) {
                if (Math.abs(A[k][i]) > Math.abs(A[maxRow][i])) {
                maxRow = k;
                }
            }
            [A[i], A[maxRow]] = [A[maxRow], A[i]];
            [b[i], b[maxRow]] = [b[maxRow], b[i]];
            for (let k = i + 1; k < n; k++) {
                const factor = A[k][i] / A[i][i];
                for (let j = i; j < n; j++) {
                A[k][j] -= factor * A[i][j];
                }
                b[k] -= factor * b[i];
            }
            }
            const x = Array(n).fill(0);
            for (let i = n - 1; i >= 0; i--) {
            let sum = 0;
            for (let j = i + 1; j < n; j++) {
                sum += A[i][j] * x[j];
            }
            x[i] = (b[i] - sum) / A[i][i];
            }
            return x;
        },
        measure: (x,slope,intercept)=>{
            const min = Math.min(...x),
            max = Math.max(...x),
            ypos=[];
            for(i=min;i<=max;i++){
                ypos.push(slope*i+intercept);
            }
            return ypos;
        }
    },
    format:{
        /**
         * Fixed decimal places
         * @param {Number} n Number to fix
         * @param {Number} d Decimal places to fix
         * @returns {Number}
         */
        fixed: (n,d)=>{
            if(n>Math.floor(n)&&n<Math.ceil(n)) return parseFloat(n.toFixed(d));
            else return n;
        }
    }
};